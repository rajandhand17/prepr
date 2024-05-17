<?php

namespace App\Http\Resources\Discussion;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'full_name'                         => $this->full_name,
            'username'                          => $this->username,
            'profile_image'                     => $this->profile_image,
        ];
    }
}
