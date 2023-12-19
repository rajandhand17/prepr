<?php

namespace App\Services\Public;

use App\Models\MemberManagement;

class MemberManagementService
{
    public static function checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            $module_type = config('constants.member_management_component_type.lab');
            $member_manger = MemberManagement::where(['module_id'=>$checkComponentBasedOnSlug->id, 'module_type'=>$module_type, 'email' => $request->email, 'invite_status'=>'2'])->first();
            if ($member_manger) {
                return true;
            }
            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            $module_type = config('constants.member_management_component_type.lab');
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
}
