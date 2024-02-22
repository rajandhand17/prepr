<?php

namespace App\Services\Chat;

use App\Exceptions\Chat\InsufficientConversationMember;
use App\Jobs\ProcessConversationCreated;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationSeenMessage;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * @throws InsufficientConversationMember
     */
    private function prepareConversationData(array $data): array
    {
        $userIds = collect([...$data['users'], auth()->user()->id])->unique()->map(function ($item) {
            return (int)$item;
        });
        $data['users'] = $userIds->toArray();


        if (count($userIds) < 2) {
            throw new InsufficientConversationMember("Conversation should contains at least 2 members");
        }

        if ($data['type'] === 'message' && count($userIds) > 2) {
            $data['type'] = config('constants.conversation_type.groupMessage');
        } else if ($data['type'] === 'message' && count($userIds) == 2) {
            $data['type'] = config('constants.conversation_type.directMessage');
        }

        return $data;
    }

    private function getConversationIdHavingUsers(array $userIds)
    {
        $userIdsTuple = implode(',', $userIds);

        $conversationIds = Conversation::select('conversations.id as conversation_id')
            ->where('is_archived', false)
            ->join('conversation_users', 'conversation_users.conversation_id', '=', 'conversations.id')
            ->groupBy('conversation_id')
            ->havingRaw("COUNT(DISTINCT conversation_users.user_id) = ?", [count($userIds)])
            ->havingRaw("SUM(CASE WHEN conversation_users.user_id NOT IN ($userIdsTuple) THEN 1 ELSE 0 END) = 0")
            ->first();

        return data_get($conversationIds, 'conversation_id');
    }

    private function isConversationAlreadyExists(array $userIds): bool
    {
        if ($this->getConversationIdHavingUsers($userIds)) {
            return true;
        }

        return false;
    }

    private function getConversationByUsers(array $userIds): Model|Collection|Builder|array|null
    {
        $conversationId = $this->getConversationIdHavingUsers($userIds);

        if ($conversationId) {
            return $this->getConversationById($conversationId);
        }

        return null;
    }

    private function getConversationById(int $id): Model|Collection|Builder|array|null
    {
        return Conversation::find($id);
    }

    /**
     * @throws Exception
     */
    private function createNewConversation(array $data): Model|Builder
    {
        try {
            DB::beginTransaction();
            $uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $conversation = Conversation::create([
                'uuid' => $uuid,
                'name' => null,
                'type' => $data['type'],
                'group_photo' => $data['group_photo'] ?? null,
                'created_by' => auth()->user()->id
            ]);
            $conversation->users()->attach($data['users']);
            foreach ($data['users'] as $id) {
                dispatch(new ProcessConversationCreated($conversation, $id))->onQueue('chat');
            }

            DB::commit();
            return $conversation;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public function start(array $data): Model|Collection|Builder|array|null
    {
        $preparedData = $this->prepareConversationData($data);

        if ($this->isConversationAlreadyExists($preparedData['users'])) {
            return $this->getConversationByUsers($preparedData['users']);
        }

        return $this->createNewConversation($preparedData);
    }

    public function listConversation(string $type): LengthAwarePaginator
    {
        $conversation = Conversation::query()
            ->with(['lastMessage', 'lastSeenMessage', 'users'])
            ->whereHas('users', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->orderByDesc(
                ConversationMessage::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1)
            );
        switch ($type) {
            case 'archive':
                $conversation->where('is_archived', true);
                break;
            case 'non-archive':
                $conversation->where('is_archived', false);
        }

        if (request()->has('search')) {
            $searchText = request()->search;
            $conversation->where(function ($query) use ($searchText) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchText) . '%'])
                    ->orWhereHas('users', function ($query) use ($searchText) {
                        $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($searchText) . '%']);
                    });
            });
        }

        return $conversation->paginate(config('site-settings.pagination_per_page'));
    }

    public function markAsSeen($conversationId, $userId, $messageId): Model|Builder
    {
        return ConversationSeenMessage::with('user')->updateOrCreate(['conversation_id' => $conversationId, "user_id" => $userId], ['message_id' => $messageId]);
    }

    public function getConversationByUUID(string $uuid)
    {
        $conversation = Conversation::where('uuid', $uuid)->first();

        if (!$conversation) {
            throw (new ModelNotFoundException())->setModel(Conversation::class);
        }

        return $conversation;
    }

    private function archive(int $id): int
    {
        return Conversation::where('id', $id)->update(['is_archived' => true]);
    }

    private function delete(string $uuid): void
    {
        Conversation::where('uuid', $uuid)->delete();
    }

    /**
     * @throws Exception
     */
    public function archiveOrSeenOrDelete(string $uuid, $action)
    {
        $conversation = $this->getConversationByUUID($uuid);
        $message = '';
        switch ($action) {
            case "archive":
                $this->archive($conversation->id);
                $message = "Conversation successfully archived";
                break;
            case 'seen':
                $this->markAsSeen($conversation->id, auth()->user()->id, data_get($conversation->lastMessage()->first(), 'id'));
                $message = "conversation is marked as seen";
                break;
            case 'delete':
                $this->delete($uuid);
                $message = "conversation is marked as delete";
                break;
            default:
                throw new Exception('action can be either archive, seen or delete');
        }

        return $message;
    }
}
