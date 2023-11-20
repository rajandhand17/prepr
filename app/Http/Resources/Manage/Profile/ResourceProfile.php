<?php

namespace App\Http\Resources\Manage\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceProfile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response=[
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'profile_image'=>$this->profile_image,
        ];
        if($this->userPersonal){
            $response['personal']['about']=$this->userPersonal->about;
            $response['personal']['gender']=$this->userPersonal->gender;
            $response['personal']['date_of_birth']=$this->userPersonal->date_of_birth;
            $response['personal']['purpose']=$this->userPersonal->purpose;
            $response['personal']['user_type']=$this->userPersonal->user_type;
            $response['personal']['recent_immigrant']=$this->userPersonal->recent_immigrant;
            $response['personal']['indigenous_group']=$this->userPersonal->indigenous_group;
        }
        if($this->userAddress){
            $response['userAddress']['latitude']=$this->userAddress->latitude;
            $response['userAddress']['longitude']=$this->userAddress->longitude;
            $response['userAddress']['address']=$this->userAddress->address;
            $response['userAddress']['city']=$this->userAddress->city;
            $response['userAddress']['state']=$this->userAddress->state;
            $response['userAddress']['country']=$this->userAddress->country;
            $response['userAddress']['zip_code']=$this->userAddress->zip_code;
        }
        return $response;
    }
}
