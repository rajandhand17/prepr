<?php

namespace App\Http\Resources\Public\Notification;

use App\Notifications\NotificationTypes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'type' => $this->type,
            'data' => $this->getDataViaType(),
            'read' => (bool)$this->read_at,
            'created_at' => $this->created_at
        ];
    }

    public function getDataViaType()
    {
        $data = [
            NotificationTypes::LAB => [
                ...data_get($this, 'formatted_lab', []),
                'type' => data_get($this->data, 'type')
            ],
            NotificationTypes::ORGANIZATION => [
                ...data_get($this, 'formatted_organization', []),
                'role' => data_get($this->data, 'additional.role'),
                'type' => data_get($this->data, 'type')
            ],
            NotificationTypes::CHALLENGE => [
                ...data_get($this, 'formatted_challenge', []),
                'type' => data_get($this->data, 'type'),
            ],
            NotificationTypes::FRIEND_REQUEST => data_get($this, 'friend_request_from'),
            NotificationTypes::LEARNING_POINT => $this->data
        ];
        return data_get($data, $this->type);
    }
}
