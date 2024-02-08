<?php

namespace App\Services\Chat;

use App\Jobs\ProcessConversationCreated;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UserMessageSeen;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    use  ConversationHelper;

    private function getConversationIdHavingUsers(array $userIds)
    {
        $userIdsTuple = implode(',', $userIds);

        $conversationIds = Conversation::select('conversations.id as conversation_id')
            ->where('is_archived', false)
            ->join('conversation_user', 'conversation_user.conversation_id', '=', 'conversations.id')
            ->groupBy('conversation_id')
            ->havingRaw("COUNT(DISTINCT conversation_user.user_id) = ?", [count($userIds)])
            ->havingRaw("SUM(CASE WHEN conversation_user.user_id NOT IN ($userIdsTuple) THEN 1 ELSE 0 END) = 0")
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

    public function listConversation(): LengthAwarePaginator
    {
        $conversation = Conversation::query()
            ->with(['lastMessage', 'lastSeenMessage', 'users'])
            ->where('is_archived', false)
            ->whereHas('users', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1)
            );

        if (request()->has('is_archived') && request()->is_archived == 'true') {
            $conversation->where('is_archived', true);
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

    public function markConversationAsSeen($conversationId, $userId, $messageId): Model|Builder
    {
        return UserMessageSeen::with('user')->updateOrCreate(['conversation_id' => $conversationId, "user_id" => $userId], ['message_id' => $messageId]);
    }
}
