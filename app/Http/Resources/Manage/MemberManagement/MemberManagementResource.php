<?php

namespace App\Http\Resources\Manage\MemberManagement;

use App\Helpers\UtilityHelper;
use App\Services\ModuleCompletionStatusService;
use App\Services\ProjectService;
use App\Services\UserService;
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
        $userRank = 0;
        $achievementCount = 0;
        if ($user) {
            $this->invitee_name = $user->first_name.' '.$user->last_name;
            $username = $user->username;
            $userRank = ($user->user_rank != null) ? $user->user_rank : 0;
            $achievementCount = ($user->achievement_count != null) ? $user->achievement_count : 0;
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

        $module_progress = [];
        if ($request->component == 'lab') {
            $moduleId = $this->module_id;
            $moduleType = '0';
            $userData = UserService::getUserByEmail($this->email);
            $module_status = 'not_started';
            $module_progress = [
                'status'        => $module_status,
                'percentage'    => '0',
            ];
            if ($userData) {
                $moduleProgress = ModuleCompletionStatusService::fetchModuleIdBasedProgress($moduleId, $moduleType, $userData->id);
                if ($moduleProgress) {
                    switch ($moduleProgress->status) {
                        case '0':
                            $module_status = 'not_started';
                            break;
                        case '1':
                            $module_status = 'in_progress';
                            break;
                        case '2':
                            $module_status = 'completed';
                            break;
                    }

                    $module_progress = [
                        'status'        => $module_status,
                        'percentage'    => $moduleProgress->percentage,
                    ];
                }
            }
        }

        if ($request->component == 'challenge') {
            $moduleId = $this->module_id;
            $moduleType = '0';
            $userData = UserService::getUserByEmail($this->email);
            $module_status = 'not_started';
            $module_progress = [
                'status'        => $module_status,
                'percentage'    => '0',
            ];
            if ($userData) {
                $moduleProgress = ProjectService::checkUserChallengeStatus($moduleId, $userData->id);
                if ($moduleProgress) {
                    switch ($moduleProgress->is_submitted) {
                        case '0':
                            $module_status = 'not_submitted';
                            break;
                        case '1':
                            $module_status = 'submitted';
                            break;
                        case '2':
                            $module_status = 'late_submitted';
                            break;
                    }
                    $module_progress = [
                        'title'         => $moduleProgress->title,
                        'status'        => $module_status,
                        'percentage'    => $moduleProgress->percentage,
                    ];
                }
            }
        }

        return [
            'id'               => $this->uuid,
            'type'             => $type,
            'invite_type'      => $invite_type,
            'request_status'   => $this->type == '1' ? $invite_status : null,
            'name'             => $this->invitee_name,
            'email'            => $this->email,
            'username'         => $username,
            'user_rank'        => $userRank,
            'achievement_count'=> $achievementCount,
            'invited_by'       => UserService::joinName($invtee_user->first_name, $invtee_user->last_name),
            'role'             => $this->role,
            'invite_status'    => $invite_status,
            'module_progress'  => $module_progress,
            'auto_invite'      => $auto_invite,
            'email_status'     => $email_status,
            'subject'          => $this->subject_line,
            'email_content'    => $this->email_body,
            'joined_at'        => !empty($this->updated_at) ? UtilityHelper::formatDateTime($this->updated_at) : null,
        ];
    }
}
