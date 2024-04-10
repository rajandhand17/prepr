<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'first_name'                        => $this->first_name,
            'last_name'                         => $this->last_name,
            'full_name'                         => $this->full_name,
            'username'                          => $this->username,
            'email'                             => $this->email,
            'profile_image'                     => $this->profile_image,
        ];
    }
}
