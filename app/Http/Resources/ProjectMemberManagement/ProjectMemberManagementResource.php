<?php

namespace App\Http\Resources\ProjectMemberManagement;

use App\Helpers\UtilityHelper;
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
        switch ($this->invite_type) {
            case '0':
                $invite_type = 'Email';
                break;
            case '1':
                $invite_type = 'Network';
                break;
            case '2':
                $invite_type = 'CSV Upload';
                break;
            case '3':
                $invite_type = 'Link';
                break;
            default:
                $invite_type = 'Network';
                break;
        }

        switch ($this->invite_status) {
            case '0':
                $invite_status = 'Invited';
                break;
            case '1':
                $invite_status = 'Accepted';
                break;
            case '2':
                $invite_status = 'Pending';
                break;
            case '3':
                $invite_status = 'Declined';
                break;
            default:
                $invite_status = 'Pending';
                break;
        }

        switch ($this->email_status) {
            case '0':
                $email_status = 'Scheduled';
                break;
            case '1':
                $email_status = 'Sent';
                break;
            case '2':
                $email_status = 'Failed';
                break;
            default:
                $email_status = 'NA';
                break;
        }

        $inviter_access_level = 'Viewer';
        if ($this->inviter_access_level) {
            switch ($this->inviter_access_level) {
                case '0':
                    $inviter_access_level = 'Viewer';
                    break;
                case '1':
                    $inviter_access_level = 'Editor';
                    break;
                case '2':
                    $inviter_access_level = 'Team Leader';
                    break;
                default:
                    $inviter_access_level = 'Viewer';
                    break;
            }
        }

        return [
            'id'                    => $this->uuid,
            'invite_type'           => $invite_type,
            'name'                  => $this->invitee_name,
            'email'                 => $this->email,
            'username'              => $username,
            'invited_by'            => UserService::joinName($invtee_user->first_name, $invtee_user->last_name),
            'role'                  => $inviter_access_level,
            'invite_status'         => $invite_status,
            'email_status'          => $email_status,
            'subject'               => $this->subject_line,
            'email_content'         => $this->email_body,
            'joined_at'             => !empty($this->updated_at) ? UtilityHelper::formatDateTime($this->updated_at) : null,
        ];
    }
}
