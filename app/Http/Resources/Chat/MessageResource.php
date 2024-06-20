<?php

namespace App\Http\Resources\Chat;

use App\Helpers\UtilityHelper;
use App\Http\Resources\User\UserSearchResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'uuid'        => $this->uuid,
            'message'     => $this->message,
            'attachments' => $this->attachments,
            'sender'      => UserSearchResource::make($this->sender),
            'is_sender'   => $this->is_sender ? 'yes' : 'no',
            'seen_users'  => UserSearchResource::collection($this->chat_seen_user),
            'sent_at'     => $this->created_at,
        ];
    }
}
