<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ProjectMemberManagement;
use Exception;

class ProjectMemberManagementService
{
    public static function checkJoinUnjoinStatus($request, $projectData)
    {
        try {
            $project_member = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $request->email, 'invite_status' => '2'])->where('invite_type', '<>', '3')->first();
            if ($project_member) {
                return true;
            }

            return false;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function acceptOrRejectJoinRequest($request, $projectData, $action)
    {
        try {
            switch($action) {
                case 'accept':
                    $invite_status = config('constants.member_management_invite_status.accepted');
                    break;
                case 'decline':
                    $invite_status = config('constants.member_management_invite_status.declined');
                    break;
            }
            $project_member = ProjectMemberManagement::where(['email' => $request->email, 'project_id' => $projectData->id, 'invite_status' => '2'])->where('invite_type', '<>', '3')->first();
            if ($project_member) {
                $project_member->update(['inviter_id' => auth()->user()->id, 'invite_status' => $invite_status]);
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function projectRequestIds($userData, $inviteStatus)
    {
        try {
            $projectRequestIds = ProjectMemberManagement::where(['invite_status' => $inviteStatus, 'email' => $userData->email])->pluck('project_id');

            return $projectRequestIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
