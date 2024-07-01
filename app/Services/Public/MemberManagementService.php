<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;

class MemberManagementService
{
    public static function checkComponentJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                case 'lab':
                    $module_type = config('constants.member_management_component_type.lab');
                    break;
                case 'challenge':
                    $module_type = config('constants.member_management_component_type.challenge');
                    break;
                default:
                    $module_type = null;
                    break;
            }
            $member_manger = MemberManagement::where(['module_id'=>$checkComponentBasedOnSlug->id, 'module_type'=>$module_type, 'email' => $request->email, 'invite_status'=>'2'])->first();
            if ($member_manger) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function acceptOrRejectComponentJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                case 'lab':
                    $module_type = config('constants.member_management_component_type.lab');
                    break;
                case 'challenge':
                    $module_type = config('constants.member_management_component_type.challenge');
                    break;
                default:
                    $module_type = null;
                    break;
            }
            switch($action) {
                case 'accept':
                    $invite_status = config('constants.member_management_invite_status.accepted');
                    break;
                case 'decline':
                    $invite_status = config('constants.member_management_invite_status.declined');
                    break;
            }
            $member_manager = MemberManagement::where(['email' => $request->email, 'module_id'=>$checkComponentBasedOnSlug->id, 'module_type'=>$module_type, 'invite_status'=>'2'])->first();
            if ($member_manager) {
                $member_manager->update(['inviter_id' => auth()->user()->id, 'invite_status' => $invite_status]);
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLatestIdsBasedOnModule($moduleType)
    {
        try {
            $moduleIds = MemberManagement::select('module_id')
                ->selectRaw('COUNT(email) as email_count')
                ->where(['module_type'=>$moduleType, 'invite_status'=>'1'])
                ->groupBy('module_id')
                ->orderByDesc('email_count')
                ->limit(config('site-settings.explore_page_limit_min'))
                ->pluck('module_id');

            return $moduleIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchComponentBasedOrganizationIds($userEmail)
    {
        try {
            // Fetch all member management records for the user
            $memberRecords = MemberManagement::where('email', $userEmail)
                ->where('invite_status', '1')
                ->get(['module_id', 'module_type']);

            $organizationIds = collect();
            $moduleIdsByType = $memberRecords->groupBy('module_type');
            if (isset($moduleIdsByType[0])) {
                $organizationIds = $organizationIds->merge($moduleIdsByType[0]->pluck('module_id'));
            }

            if (isset($moduleIdsByType[1])) {
                $fetchLabOrganizations = LabService::fetchLabOrganizations($moduleIdsByType[1]->pluck('module_id'));
                $organizationIds = $organizationIds->merge($fetchLabOrganizations);
            }

            if (isset($moduleIdsByType[2])) {
                $fetchChallengeOrganizations = ChallengeService::fetchChallengeOrganizations($moduleIdsByType[2]->pluck('module_id'));
                $organizationIds = $organizationIds->merge($fetchChallengeOrganizations);
            }

            return $organizationIds->unique()->values();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
