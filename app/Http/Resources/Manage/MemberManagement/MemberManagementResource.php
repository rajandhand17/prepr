<?php

namespace App\Http\Resources\Manage\MemberManagement;

use App\Helpers\UtilityHelper;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberManagementResource extends JsonResource
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
        $type = ($this->type == '0') ? 'Invitation' : (($this->type == '1') ? 'Join Request' : 'Auto Created');
        $invite_types = [
            '0' => 'Email',
            '1' => 'Network',
            '2' => 'Join Request',
            '3' => 'CSV Upload',
            '4' => 'From HR Integration',
        ];
        $invite_type = data_get($invite_types, $this->invite_type);
        $invtee_user = UserService::getUserById($this->inviter_id);
        $invite_status = ($this->invite_status == '0') ? 'Invited' : (($this->invite_status == '1') ? 'Accepted' : (($this->invite_status == '2') ? 'Pending' : (($this->invite_status == '3') ? 'Declined' : 'Auto Created')));
        $auto_invite = ($this->auto_invite == '0') ? 'No' : 'Yes';
        $email_status = ($this->email_status == '0') ? 'Scheduled' : (($this->email_status == '1') ? 'Sent' : (($this->email_status == '2') ? 'Failed' : 'NA'));

        return [
            'id'            => $this->uuid,
            'type'          => $type,
            'invite_type'   => $invite_type,
            'name'          => $this->invitee_name,
            'email'         => $this->email,
            'username'      => $username,
            'invited_by'    => UserService::joinName($invtee_user->first_name, $invtee_user->last_name),
            'role'          => $this->role,
            'invite_status' => $invite_status,
            'auto_invite'   => $auto_invite,
            'email_status'  => $email_status,
            'subject'       => $this->subject_line,
            'email_content' => $this->email_body,
            'joined_at'     => !empty($this->created_at) ? UtilityHelper::formatDateTime($this->created_at) : null,
        ];
    }
}
