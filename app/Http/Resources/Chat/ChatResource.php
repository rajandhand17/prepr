<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'message' => $this->message,
            'attachment' => $this->attachment,
            'sender' => ChatUserResource::make($this->sender),
            "seen_users" => ChatUserResource::collection($this->chat_seen_user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
