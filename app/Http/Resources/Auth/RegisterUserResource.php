<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       
        return [
            "id"=>$this->id,
            "device_token"=>$this->device_token,
            "name"=>$this->name,
            "first_name"=>$this->first_name,
            "last_name"=>$this->last_name,
            "username"=>$this->username,
            "email"=>$this->email,
            "password"=>$this->password,
            "country_code"=>$this->country_code,
            "verification"=>$this->verification,
            "two_factor"=>$this->two_factor,
            "two_factor_otp"=>$this->two_factor_otp,
            "is_login"=>$this->is_login,
            "profile_image"=>$this->profile_image,
            "phone_number"=>$this->phone_number,
            "fr_request"=>$this->fr_request,
            "fr_accept"=>$this->fr_accept,
            "point"=>$this->point,
            "rank"=>$this->rank,
            "remember_token"=>$this->remember_token,
            "is_verify"=>$this->is_verify,
            "is_email_sent"=>$this->is_email_sent,
            "verify_token"=>$this->verify_token,
            "mycode"=>$this->mycode,
            "isReferralOpen"=>$this->isReferralOpen,
            "manage_alerts"=>$this->manage_alerts,
            "is_subscribe"=>$this->is_subscribe,
        ];
    }
}
