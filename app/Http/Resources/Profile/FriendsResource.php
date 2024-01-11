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
        if($this->reference_id==auth()->user()->id){
            $data=[
                'id'           => $this->getFriendsProfileBasedOnUserId->id,
                'first_name'   => $this->getFriendsProfileBasedOnUserId->first_name,
                'last_name'    => $this->getFriendsProfileBasedOnUserId->last_name,
                'full_name'    => $this->getFriendsProfileBasedOnUserId->full_name,
                'username'     => $this->getFriendsProfileBasedOnUserId->username,
                'email'        => $this->getFriendsProfileBasedOnUserId->email,
                'role'         => 'user',
                'country_code' => $this->getFriendsProfileBasedOnUserId->country_code,
                'phone_number' => $this->getFriendsProfileBasedOnUserId->phone_number,
                'profile_image'=> $this->getFriendsProfileBasedOnUserId->profile_image,
            ];
        }else{
            $data=[
                'id'           => $this->getFriendsProfilebasedOnReference->id,
                'first_name'   => $this->getFriendsProfilebasedOnReference->first_name,
                'last_name'    => $this->getFriendsProfilebasedOnReference->last_name,
                'full_name'    => $this->getFriendsProfilebasedOnReference->full_name,
                'username'     => $this->getFriendsProfilebasedOnReference->username,
                'email'        => $this->getFriendsProfilebasedOnReference->email,
                'role'         => 'user',
                'country_code' => $this->getFriendsProfilebasedOnReference->country_code,
                'phone_number' => $this->getFriendsProfilebasedOnReference->phone_number,
                'profile_image'=> $this->getFriendsProfilebasedOnReference->profile_image,
            ];

        }
        return $data;
    }
}
