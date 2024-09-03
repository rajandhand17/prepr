<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPrivacyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile_visibility'                  => config('constants.privacy_options.'.$this->profile_privacy),
            'project_visibility'                  => config('constants.privacy_options.'.$this->project_privacy),
            'friend_request_privacy'              => config('constants.friend_request_options.'.$this->friend_request_privacy),
        ];
    }
}
