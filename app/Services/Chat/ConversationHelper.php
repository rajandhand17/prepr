<?php

namespace App\Services\Chat;

use App\Enum\ConversationType;
use App\Exceptions\Chat\InsufficientConversationMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait ConversationHelper
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

        if ($data['type'] === ConversationType::MESSAGE->value && count($userIds) > 2) {
            $data['type'] = config('constants.conversation_type.groupMessage');
        } else if ($data['type'] === ConversationType::MESSAGE->value && count($userIds) == 2) {
            $data['type'] = config('constants.conversation_type.directMessage');
        }

        return $data;
    }

    public function getGroupName(array $userIds): string
    {
        $users = User::query()->whereIn('id', $userIds)->get();

        return $this->prepareGroupName($users);
    }



}
