<?php

namespace App\Services\Chat;

use Exception;
use App\Models\User;
use App\Notifications\ConversationArchived;
use App\Notifications\ConversationCreated;
use App\Notifications\ConversationDeleted;
use HiFolks\RandoPhp\Randomize;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationSeenMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ConversationService
{

    private function prepareData(array $data)
    {
        try {
            $data['usernames'][] = auth()->user()->username;
            $userIds = User::whereIn('username', $data['usernames'])
                ->pluck('id')
                ->toArray();

            $data['users'] = $userIds;
            if (count($userIds) < 2) {
                return false;
            }

            if ($data['type'] === 'message' && count($userIds) > 2) {
                $data['type'] = config('constants.conversation_type.group_message');
            } else if ($data['type'] === 'message' && count($userIds) == 2) {
                $data['type'] = config('constants.conversation_type.direct_message');
            } else if ($data['type'] === 'announcement') {
                $data['type'] = config('constants.conversation_type.announcement');
            }

            return $data;
        } catch (Exception $e) {
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
                ->havingRaw("COUNT(DISTINCT conversation_users.user_id) = ?", [count($userIds)])
                ->havingRaw("SUM(CASE WHEN conversation_users.user_id NOT IN ($userIdsTuple) THEN 1 ELSE 0 END) = 0")
                ->first();

            return data_get($conversationIds, 'conversation_id');
        } catch (Exception $e) {
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
            return false;
        }
    }

    private function getById(int $id)
    {
        try {
            return Conversation::find($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function addMembers($conversation, $users)
    {
        try {
            $conversation->users()->attach($users);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function notify($conversationId, $type, $deletedUserIds = [])
    {
        try {
            if ($type === 'delete') {
                $conversationUserIds = $deletedUserIds;
            } else if ($type === 'archived' || $type === 'created') {
                $conversationUserIds = $this->getConversationUsersId($conversationId);
            } else {
                return false;
            }

            $conversation = $this->getById($conversationId);
            $userIds = array_filter($conversationUserIds, function ($item) {
                return $item !== auth()->user()->id;
            });

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
                default:
                    return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getConversationUsersId($conversationId)
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);
            return $conversation->users()->pluck('id')->toArray();
        } catch (Exception $e) {
            return false;
        }
    }

    private function store(array $data)
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
            $conversationUsers = $this->addMembers($conversation, $data['users']);

            if (!$conversationUsers) {
                DB::rollBack();
                return false;
            }

            $notification = $this->notify($conversation->id, 'created');

            if (!$notification) {
                return false;
            }

            DB::commit();
            return $conversation;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function markAsSeen($conversationId, $userId, $messageId)
    {
        try {
            return ConversationSeenMessage::with('user')->updateOrCreate(['conversation_id' => $conversationId, "user_id" => $userId], ['message_id' => $messageId]);
        } catch (Exception $e) {
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
            return false;
        }
    }

    public function create(array $data)
    {
        try {
            $preparedData = $this->prepareData($data);

            if ($this->isConversationAlreadyExists($preparedData['users'], $preparedData['type'])) {
                return $this->getConversationByUsers($preparedData['users'], $preparedData['type']);
            }
            return $this->store($preparedData);
        } catch (Exception $e) {
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
                    break;
                default:
                    return false;
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
        } catch (Exception $e) {
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
            return false;
        }
    }


    public function archiveOrSeenOrDelete(string $uuid, $action)
    {
        try {
            $conversation = $this->getByUUID($uuid);

            if (!$conversation) {
                return false;
            }
            switch ($action) {
                case "archive":
                    $this->archive($conversation->id);
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
            return false;
        }
    }
}
