<?php

namespace App\Http\Resources\Chat;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Settings\UserNotificationResource;
use App\Http\Resources\Settings\UserPrivacyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatUserResource extends JsonResource
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
            'preferred_language' => $this->preferred_language,
            'preferred_timezone' => $this->preferred_timezone ? $this->preferred_timezone : 'EST',
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'profile_image' => $this->profile_image,
        ];
    }
}
