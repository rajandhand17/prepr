<?php

namespace App\Http\Resources\Manage\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $response['user']['id'] = $this->id;
        $response['user']['username'] = $this->username;
        $response['user']['email'] = $this->email;
        $response['user']['profile_image'] = $this->profile_image;
        if ($this->userPersonal) {
            $response['user_personal']['about'] = $this->userPersonal->about;
            $response['user_personal']['gender'] = $this->userPersonal->gender;
            $response['user_personal']['date_of_birth'] = $this->userPersonal->date_of_birth;
            $response['user_personal']['purpose'] = $this->userPersonal->purpose;
            $response['user_personal']['user_type'] = $this->userPersonal->user_type;
            $response['user_personal']['recent_immigrant'] = $this->userPersonal->recent_immigrant;
            $response['user_personal']['indigenous_group'] = $this->userPersonal->indigenous_group;
        }
        if ($this->userAddress) {
            $response['userAddress']['latitude'] = $this->userAddress->latitude;
            $response['userAddress']['longitude'] = $this->userAddress->longitude;
            $response['userAddress']['address'] = $this->userAddress->address;
            $response['userAddress']['city'] = $this->userAddress->city;
            $response['userAddress']['state'] = $this->userAddress->state;
            $response['userAddress']['country'] = $this->userAddress->country;
            $response['userAddress']['zip_code'] = $this->userAddress->zip_code;
        }

        return $response;
    }
}
