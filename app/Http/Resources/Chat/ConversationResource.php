<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserSearchResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'uuid'                      => $this->uuid,
            'is_archived'               => $this->is_archived ? 'yes' : 'no',
            'type'                      => $this->type,
            'last_message'              => MessageResource::make($this->lastMessage),
            'online'                    => $this->is_online ? 'yes' : 'no',
            'seen'                      => $this->is_conversation_seen ? 'yes' : 'no',
            'name'                      => $this->default_conversation_name,
            'is_private'                => $this->is_private ? 'yes' : 'no',
            'users_count'               => $this->users()->count() ?? '0',
            'created_at'                => $this->created_at,
            'members'                   => UserSearchResource::collection($this->users),
        ];
    }
}
