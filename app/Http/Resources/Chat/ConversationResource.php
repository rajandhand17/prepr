<?php

namespace App\Http\Resources\Chat;

use App\Helpers\UtilityHelper;
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
            "id" => $this->id,
            "uuid" => $this->uuid,
            "is_archived" => $this->is_archived ? 'yes' : 'no',
            "type" => $this->type,
            "last_message" => MessageResource::make($this->lastMessage),
            "is_conversation_seen" => $this->is_conversation_seen,
            "default_conversation_name" => $this->default_conversation_name,
            "last_seen_message" => MessageResource::make(data_get($this->lastSeenMessage, 'chat')),
            "is_private" => $this->is_private ? 'yes' : 'no',
        ];
    }
}
