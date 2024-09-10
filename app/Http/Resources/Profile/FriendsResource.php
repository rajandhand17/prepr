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
        $profile = $this->reference_id == auth('api')->id() ? $this->getFriendsProfileBasedOnUserId : $this->getFriendsProfilebasedOnReference;
        $data = [];
        if ($profile) {
            $data = [
                'id'            => $profile->id,
                'first_name'    => $profile->first_name,
                'last_name'     => $profile->last_name,
                'full_name'     => $profile->full_name,
                'username'      => $profile->username,
                'email'         => $profile->email,
                'role'          => 'user',
                'country_code'  => $profile->country_code,
                'phone_number'  => $profile->phone_number,
                'profile_image' => $profile->profile_image,
                'learnrank'     => $profile->user_rank,
                'updated_at'    => $profile->updated_at,
            ];
        }

        return $data;
    }
}
