<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
                'id'           =>$this->getFriendsProfile->id,
                'first_name'   => $this->getFriendsProfile->first_name,
                'last_name'    => $this->getFriendsProfile->last_name,
                'full_name'    => $this->getFriendsProfile->full_name,
                'username'     => $this->getFriendsProfile->username,
                'email'        =>$this->getFriendsProfile->email,
                'role'         =>'user',
                'country_code' =>$this->getFriendsProfile->country_code,
                'phone_number' =>$this->getFriendsProfile->phone_number,
                'profile_image'=>$this->getFriendsProfile->profile_image,
        ];
    }
}
