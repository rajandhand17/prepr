<?php

namespace App\Services\Chat;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Chat\ConversationResource;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationSeenMessage;
use App\Models\User;
use App\Notifications\AnnouncementConversationCreated;
use App\Notifications\ConversationArchived;
use App\Notifications\ConversationCreated;
use App\Notifications\ConversationDeleted;
use App\Notifications\ConversationUnArchived;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ConversationService
{
    private function prepareData(array $data)
    {
        try {
            if ($data['type'] != 'announcement') {
                $data['usernames'][] = auth()->user()->username;
            }
            $userIds = User::whereIn('username', $data['usernames'])
                ->pluck('id')
                ->toArray();

            $data['users'] = $userIds;
            if (count($userIds) < 2) {
                return false;
            }

            if ($data['type'] === 'message' && count($userIds) > 2) {
                $data['type'] = config('constants.conversation_type.group_message');
            } elseif ($data['type'] === 'message' && count($userIds) == 2) {
                $data['type'] = config('constants.conversation_type.direct_message');
            } elseif ($data['type'] === 'announcement') {
                $data['type'] = config('constants.conversation_type.announcement');
            }

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getConversationIdHavingUsers(array $userIds, $type)
    {
        try {
            $userIdsTuple = implode(',', $userIds);

            $conversationIds = Conversation::select('conversations.id as conversation_id')
                ->where('is_archived', false)
                ->where('type', $type)
                ->join('conversation_users', 'conversation_users.conversation_id', '=', 'conversations.id')
                ->groupBy('conversation_id')
                ->havingRaw('COUNT(DISTINCT conversation_users.user_id) = ?', [count($userIds)])
                ->havingRaw("SUM(CASE WHEN conversation_users.user_id NOT IN ($userIdsTuple) THEN 1 ELSE 0 END) = 0")
                ->first();

            return data_get($conversationIds, 'conversation_id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function isConversationAlreadyExists(array $userIds, $type)
    {
        try {
            if ($this->getConversationIdHavingUsers($userIds, $type)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getConversationByUsers(array $userIds, $type)
    {
        try {
            $conversationId = $this->getConversationIdHavingUsers($userIds, $type);

            if ($conversationId) {
                return $this->getById($conversationId);
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getById(int $id)
    {
        try {
            return Conversation::find($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function addMembers($conversation, $users)
    {
        try {
            $conversation->users()->attach($users);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function notify($conversationId, $type, $deletedUserIds = [])
    {
        try {
            if ($type === 'delete') {
                $conversationUserIds = $deletedUserIds;
            } elseif ($type === 'archived' || $type === 'created' || $type === 'unarchived' || $type === 'announcement') {
                $conversationUserIds = $this->getConversationUsersId($conversationId);
            } else {
                return false;
            }

            $conversation = $this->getById($conversationId);
            if ($conversation) {
                $conversation = collect(ConversationResource::make($conversation));
            } else {
                $conversation = ['uuid' => request()->route()->parameter('uuid')];
            }

            if ($type === 'announcement') {
                $userIds = $conversationUserIds;
            } else {
                $userIds = array_filter($conversationUserIds, function ($item) {
                    return $item !== auth()->user()->id;
                });
            }

            $users = User::whereIn('id', $userIds)->get();
            switch ($type) {
                case 'created':
                    Notification::send($users, new ConversationCreated($conversation, $userIds));
                    break;
                case 'delete':
                    Notification::send($users, new ConversationDeleted($conversation, $userIds));
                    break;
                case 'archived':
                    Notification::send($users, new ConversationArchived($conversation, $userIds));
                    break;
                case 'unarchived':
                    Notification::send($users, new ConversationUnArchived($conversation, $userIds));
                    break;
                case 'announcement':
                    Notification::send($users, new AnnouncementConversationCreated($conversation, $userIds));
                    break;
                default:
                    return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getConversationUsersId($conversationId)
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);

            return $conversation->users()->pluck('id')->toArray();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function store(array $data)
    {
        try {
            DB::beginTransaction();
            $created_by = ($data['type'] == config('constants.conversation_type.announcement')) ? $data['created_by'] : auth()->user()->id;
            $uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $conversation = Conversation::create([
                'uuid'        => $uuid,
                'name'        => null,
                'type'        => $data['type'],
                'group_photo' => $data['group_photo'] ?? null,
                'created_by'  => $created_by,
            ]);
            $conversationUsers = $this->addMembers($conversation, $data['users']);

            if (!$conversationUsers) {
                DB::rollBack();

                return false;
            }

            $notify_type = ($data['type'] == config('constants.conversation_type.announcement')) ? 'announcement' : 'created';
            $notification = $this->notify($conversation->id, $notify_type);

            if (!$notification) {
                return false;
            }

            DB::commit();

            return $conversation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function markAsSeen($conversationId, $userId, $messageId)
    {
        try {
            return ConversationSeenMessage::with('user')->updateOrCreate(['conversation_id' => $conversationId, 'user_id' => $userId], ['message_id' => $messageId]);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function archive(int $id)
    {
        try {
            Conversation::where('id', $id)->update(['is_archived' => true]);
            $this->notify($id, 'archived');

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function unarchive(int $id)
    {
        try {
            Conversation::where('id', $id)->update(['is_archived' => false]);
            $this->notify($id, 'unarchived');

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function delete(int $id)
    {
        try {
            $userIds = $this->getConversationUsersId($id);
            Conversation::where('id', $id)->delete();
            $this->notify($id, 'delete', $userIds);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function create(array $data)
    {
        try {
            $preparedData = $this->prepareData($data);

            if ($this->isConversationAlreadyExists($preparedData['users'], $preparedData['type']) && $preparedData['type'] != config('constants.conversation_type.announcement')) {
                return $this->getConversationByUsers($preparedData['users'], $preparedData['type']);
            }

            return $this->store($preparedData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function list(string $type)
    {
        try {
            $conversation = Conversation::query()
                ->with(['lastMessage', 'lastSeenMessage', 'users'])
                ->whereHas('users', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })->orderByDesc(
                    ConversationMessage::select('updated_at')
                        ->whereColumn('conversation_id', 'conversations.id')
                        ->orderByDesc('updated_at')
                        ->limit(1)
                );

            switch ($type) {
                case 'archive':
                    $conversation->where('is_archived', true);
                    break;
                case 'inbox':
                    $conversation->where('is_archived', false);
                    break;
                default:
                    $conversation->where('is_archived', false);
            }

            if (request()->has('search')) {
                $searchText = request()->search;
                $conversation->where(function ($query) use ($searchText) {
                    $query->WhereHas('users', function ($query) use ($searchText) {
                        $query->whereRaw('LOWER(full_name) LIKE ?', ['%'.strtolower($searchText).'%']);
                        $query->orWhereRaw('LOWER(first_name) LIKE ?', ['%'.strtolower($searchText).'%']);
                        $query->orWhereRaw('LOWER(last_name) LIKE ?', ['%'.strtolower($searchText).'%']);
                    });
                });
            }

            return $conversation->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getByUUID(string $uuid)
    {
        try {
            $conversation = Conversation::where('uuid', $uuid)->first();

            if (!$conversation) {
                return false;
            }

            return $conversation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function archiveOrUnarchiveOrSeenOrDelete(string $uuid, $action)
    {
        try {
            $conversation = $this->getByUUID($uuid);
            if (!$conversation) {
                return false;
            }
            switch ($action) {
                case 'archive':
                    $this->archive($conversation->id);
                    break;
                case 'un-archive':
                    $this->unarchive($conversation->id);
                    break;
                case 'seen':
                    $this->markAsSeen($conversation->id, auth()->user()->id, data_get($conversation->lastMessage()->first(), 'id'));
                    break;
                case 'delete':
                    $this->delete($conversation->id);
                    break;
                default:
                    return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function makeOnline($id)
    {
        try {
            $user = User::query()->findOrFail($id);
            $user->presence()->updateOrCreate(['user_id' => $id], ['is_online' => true]);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function makeOffline($id)
    {
        try {
            $user = User::query()->findOrFail($id);
            $user->presence()->updateOrCreate(['user_id' => $id], ['is_online' => false]);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function onlineOrOffline($id, $action)
    {
        try {
            switch ($action) {
                case 'online':
                    $this->makeOnline($id);
                    break;
                case 'offline':
                    $this->makeOffline($id);
                    break;
                default:
                    return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function dashboardInboxList($userData)
    {
        try {
            $conversation = Conversation::whereHas('users', function ($query) use ($userData) {
                $query->where('user_id', $userData->id)->where('is_archived', false);
            })->orderByDesc(ConversationMessage::select('updated_at')
            ->whereColumn('conversation_id', 'conversations.id')
            ->orderByDesc('updated_at')
            ->limit(1))
            ->take(5)->get();

            return $conversation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
