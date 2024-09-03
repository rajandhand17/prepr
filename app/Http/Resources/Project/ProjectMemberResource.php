<?php

namespace App\Http\Resources\Project;

use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMemberResource extends JsonResource
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
        $user_name = null;
        $full_name = null;
        $profile_image = null;

        switch ($this->inviter_access_level) {
            case '0':
                $access_level = 'viewer';
                break;
            case '1':
                $access_level = 'editor';
                break;
            case '2':
                $access_level = 'team_leader';
                break;
            default:
                $access_level = 'viewer';
                break;
        }

        switch ($this->invite_status) {
            case '0':
                $joined_status = 'invited';
                break;
            case '1':
                $joined_status = 'accepted';
                break;
            case '2':
                $joined_status = 'pending';
                break;
            case '3':
                $joined_status = 'declined';
                break;
            default:
                $joined_status = 'no';
                break;
        }

        $getUserDetails = UserService::getUserByEmail($this->email);
        if ($getUserDetails) {
            $user_name = $getUserDetails->username;
            $full_name = $getUserDetails->full_name;
            $profile_image = $getUserDetails->profile_image;
        }

        return [
            'user_name'     => $user_name,
            'full_name'     => $full_name,
            'email'         => $this->email,
            'profile_image' => $profile_image,
            'joined_status' => $joined_status,
            'access_level'  => $access_level,
        ];
    }
}
