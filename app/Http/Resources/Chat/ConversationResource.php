<?php

namespace App\Http\Resources\Chat;

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
            "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "is_archived" => $this->is_archived ? true : false,
            "type" => $this->type,
            "last_message" => ChatResource::make($this->lastMessage),
            "is_conversation_seen" => $this->is_conversation_seen,
            "default_conversation_name" => $this->default_conversation_name,
            "last_seen_message" => ChatResource::make(data_get($this->lastSeenMessage, 'chat')),
            "group_photo" => $this->group_photo,
            "is_private" => $this->is_private ? true : false,
            "users" => collect($this->users)->map(function ($item) {
                return ChatUserResource::make($item);
            }),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
