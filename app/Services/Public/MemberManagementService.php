<?php

namespace App\Services\Public;

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
            return false;
        }
    }

    public static function getLatestLabsIds()
    {
        try {
            $memberManagement = MemberManagement::where('module_type', '1')
                ->orderBy('created_at')
                ->limit(6)
                ->pluck('module_id');

            return $memberManagement;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLatestChallengeIds()
    {
        try {
            $memberManagement = MemberManagement::where('module_type', '2')
                ->orderBy('created_at')
                ->limit(6)
                ->pluck('module_id');

            return $memberManagement;
        } catch (\Exception $e) {
            return false;
        }
    }
}
