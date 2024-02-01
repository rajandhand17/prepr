<?php

namespace App\Http\Resources\Settings;

use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone_number,
            'timezone'=>$this->preferred_timezone,
            'two_factor_verification'=>$this->two_factor_verification,
            'preferred_language'=>$this->preferred_language,
            'profile_image'=>$this->profile_image,
            'profile_visibility'=>$this->userSetting->profile_privacy,
            'project_visibility'=>$this->userSetting->project_privacy,
            'friend_request_privacy'=>$this->userSetting->friend_request_privacy,


        ];
    }
}
