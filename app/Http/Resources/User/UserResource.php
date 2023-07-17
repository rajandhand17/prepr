<?php

namespace App\Http\Resources\User;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        $roles = $this->roles->pluck('display_name');
        if ($roles) {
            $roles = array_unique($roles->toArray());
        } else {
            $roles = [];
        }

        return [
            'id'                         => $this->id,
            'preferred_language'         => $this->preferred_language,
            'first_name'                 => $this->first_name,
            'last_name'                  => $this->last_name,
            'full_name'                  => $this->full_name,
            'username'                   => $this->username,
            'email'                      => $this->email,
            'profile_image'              => $this->profile_image,
            'two_factor_verification'    => $this->two_factor_verification,
            'user_points'                => $this->user_points,
            'user_rank'                  => $this->verified_user,
            'verified_user'              => $this->verified_user,
            'referral_code'              => $this->referal_code,
            'is_profile_completed'       => $this->is_profile_completed,
            'member_since'               => UtilityHelper::formatDateTime($this->created_at),
            'roles'                      => $roles,
        ];
    }
}
