<?php

namespace App\Http\Resources\Discussion;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailDiscussionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'preferred_language'         => $this->preferred_language,
            'first_name'                 => $this->first_name,
            'last_name'                  => $this->last_name,
            'full_name'                  => $this->full_name,
            'username'                   => $this->username,
            'email'                      => $this->email,
            'profile_image'              => $this->profile_image,
        ];
    }
}
