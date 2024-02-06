<?php

namespace App\Http\Resources\Manage\ProjectMemberManagement;

use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMemberManagementResource extends JsonResource
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
        $user = UserService::getUserByEmail($this->email);
        $username = null;
        if ($user) {
            $this->invitee_name = $user->first_name.' '.$user->last_name;
            $username = $user->username;
        }
        $invtee_user = UserService::getUserById($this->inviter_id);
        $invite_type = ($this->invite_type == '0') ? 'Email' : (($this->invite_type == '1') ? 'Network' : (($this->invite_type == '2') ? 'CSV Upload' : (($this->invite_type == '3') ? 'Link' : 'CSV Upload')));
        $invite_status = ($this->invite_status == '0') ? 'Invited' : (($this->invite_status == '1') ? 'Accepted' : (($this->invite_status == '2') ? 'Pending' : (($this->invite_status == '3') ? 'Declined' : 'Auto Created')));
        $email_status = ($this->email_status == '0') ? 'Scheduled' : (($this->email_status == '1') ? 'Sent' : (($this->email_status == '2') ? 'Failed' : 'NA'));

        return [
            'id'            => $this->uuid,
            'invite_type'   => $invite_type,
            'name'          => $this->invitee_name,
            'email'         => $this->email,
            'username'      => $username,
            'invited_by'    => UserService::joinName($invtee_user->first_name, $invtee_user->last_name),
            'role'          => $this->role,
            'invite_status' => $invite_status,
            'email_status'  => $email_status,
            'subject'       => $this->subject_line,
            'email_content' => $this->email_body,
        ];
    }
}
