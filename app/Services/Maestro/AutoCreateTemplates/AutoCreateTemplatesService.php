<?php

namespace App\Services\Maestro\AutoCreateTemplates;

use App\Helpers\UtilityHelper;
use App\Models\AutoCreateTemplate;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\Lab;
use App\Models\LabProgram;
use Exception;

class AutoCreateTemplatesService
{
    public static function createUpdateAutoTemplate($request)
    {
        try {
            if (!empty($request->selected_role)) {
                if ($request->sdc_lab == 'true') {
                    AutoCreateTemplate::updateOrCreate(
                        ['role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected],
                        ['language'=> $request->selected_language, 'role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected, 'lab_id'=> $request->selected_lab_ids, 'lab_group_id'=> $request->selected_group_lab_ids, 'invite_labs'=>$request->invite_lab]
                    );
                }
                if ($request->sdc_lab == 'false') {
                    AutoCreateTemplate::updateOrCreate(
                        ['role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected],
                        ['language'=> $request->selected_language, 'role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected, 'lab_id'=> null, 'lab_group_id'=> null, 'invite_labs'=> '0']
                    );
                }
                if ($request->sdc_challenge == 'true') {
                    AutoCreateTemplate::updateOrCreate(
                        ['role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected],
                        ['language'=> $request->selected_language, 'role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected, 'challenge_id'=> $request->selected_challenge_ids, 'challenge_group_id'=> $request->selected_group_challenge_ids, 'invite_challenges'=>$request->invite_challenge]
                    );
                }
                if ($request->sdc_challenge == 'false') {
                    AutoCreateTemplate::updateOrCreate(
                        ['role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected],
                        ['language'=> $request->selected_language, 'role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected, 'challenge_id'=> null, 'challenge_group_id'=> null, 'invite_challenges'=> '0']
                    );
                }
                if ($request->sdc_lab == 'false' && $request->sdc_challenge == 'false') {
                    AutoCreateTemplate::updateOrCreate(
                        ['role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected],
                        ['language'=> $request->selected_language, 'role_type'=> $request->selected_role, 'role_user_type'=> $request->role_user_type_slected, 'lab_id'=> null, 'lab_group_id'=> null, 'invite_labs'=>'0', 'challenge_id'=> null, 'challenge_group_id'=> null, 'invite_challenges'=> '0']
                    );
                }

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPreSelectLabList($request)
    {
        try {
            $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'role_user_type'=> $request->role_type_selected])->pluck('lab_id')->first();
            $explodeLabIdsArray = explode(',', $getPreSelectedLabTemplates);

            $data = [];
            $labs = Lab::whereIn('id', $explodeLabIdsArray)->where('privacy', '0')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            $count = 0;
            foreach ($labs as $key => $title) {
                $labsr[$count]['id'] = $key;
                $labsr[$count]['text'] = $title;
                $count++;
            }
            $inviteInfo = self::getInviteUserInfo($request->role_selected, $request->role_type_selected);

            $data['result'] = $labsr ?? [];
            $data['invite_info'] = $inviteInfo ?? [];

            return  response()->json($data);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPreSelectedChallengeList($request)
    {
        try {
            $getPreSelectedChallengeTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'role_user_type'=> $request->role_type_selected])->pluck('challenge_id')->first();
            $explodeChallengeIdsArray = explode(',', $getPreSelectedChallengeTemplates);

            $data = [];
            $challenges = Challenge::whereIn('id', $explodeChallengeIdsArray)->where('privacy', '0')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            $count = 0;
            foreach ($challenges as $key => $title) {
                $challenegsr[$count]['id'] = $key;
                $challenegsr[$count]['text'] = $title;
                $count++;
            }
            $inviteInfo = self::getInviteUserInfo($request->role_selected, $request->role_type_selected);

            $data['result'] = $challenegsr ?? [];
            $data['invite_info'] = $inviteInfo ?? [];

            return  response()->json($data);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchLabList($request)
    {
        $searched = $request->search;

        try {
            $data = [];
            if (!empty($searched)) {
                $labs = Lab::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language)->where('title', 'like', '%'.$searched.'%')->pluck('title', 'id')->toArray();
            } else {
                $labs = Lab::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language)->pluck('title', 'id')->toArray();
            }
            $count = 0;
            foreach ($labs as $key => $title) {
                $labsr[$count]['id'] = $key;
                $labsr[$count]['text'] = $title;
                $count++;
            }
            $data['result'] = $labsr;

            return  response()->json($data);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function fetchChallengeList($request)
    {
        $searched = $request->search;

        try {
            $data = [];
            if (!empty($searched)) {
                $challenges = Challenge::orderBy('id', 'DESC')->where('language', $request->language)->where('privacy', '0')->where('title', 'like', '%'.$searched.'%')->pluck('title', 'id')->toArray();
            } else {
                $challenges = Challenge::orderBy('id', 'DESC')->where('language', $request->language)->where('privacy', '0')->pluck('title', 'id')->toArray();
            }
            $count = 0;
            foreach ($challenges as $key => $title) {
                $challengeSr[$count]['id'] = $key;
                $challengeSr[$count]['text'] = $title;
                $count++;
            }
            $data['result'] = $challengeSr;

            return  response()->json($data);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public static function fetchChallengeGroupList($request)
    {
        $searched = $request->search;

        try {
            $data = [];
            if (!empty($searched)) {
                $challengeGroups = ChallengePath::where('language', $request->language)->where('privacy', '0')->where('title', 'like', '%'.$searched.'%')->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            } else {
                $challengeGroups = ChallengePath::where('language', $request->language)->where('privacy', '0')->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            }
            $count = 0;
            foreach ($challengeGroups as $key => $title) {
                $challengeGroupSr[$count]['id'] = $key;
                $challengeGroupSr[$count]['text'] = $title;
                $count++;
            }
            $data['result'] = $challengeGroupSr;

            return  response()->json($data);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public static function fetchLabGroupList($request)
    {
        $searched = $request->search;

        try {
            $data = [];
            if (!empty($searched)) {
                $labGroups = LabProgram::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language)->where('title', 'like', '%'.$searched.'%')->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            } else {
                $labGroups = LabProgram::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            }
            $count = 0;
            foreach ($labGroups as $key => $title) {
                $labGroupSr[$count]['id'] = $key;
                $labGroupSr[$count]['text'] = $title;
                $count++;
            }
            $data['result'] = $labGroupSr;

            return  response()->json($data);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public static function getPreSelectLabGroupList($request)
    {
        try {
            $getPreSelectedLabGroupTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'role_user_type'=> $request->role_type_selected])->pluck('lab_group_id')->first();
            $explodeLabGroupIdsArray = explode(',', $getPreSelectedLabGroupTemplates);

            $data = [];
            $labGroups = LabProgram::whereIn('id', $explodeLabGroupIdsArray)->where('privacy', '0')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            $count = 0;
            foreach ($labGroups as $key => $title) {
                $labsr[$count]['id'] = $key;
                $labsr[$count]['text'] = $title;
                $count++;
            }

            $data['result'] = $labsr ?? [];

            return  response()->json($data);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPreSelectChallengeGroupList($request)
    {
        try {
            $getPreSelectedChallengeGroupTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'role_user_type'=> $request->role_type_selected])->pluck('challenge_group_id')->first();
            $explodeChallengeGroupIdsArray = explode(',', $getPreSelectedChallengeGroupTemplates);

            $data = [];
            $challengeGroups = ChallengePath::whereIn('id', $explodeChallengeGroupIdsArray)->where('privacy', '0')->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            $count = 0;
            foreach ($challengeGroups as $key => $title) {
                $challengesr[$count]['id'] = $key;
                $challengesr[$count]['text'] = $title;
                $count++;
            }

            $data['result'] = $challengesr ?? [];

            return  response()->json($data);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getInviteUserInfo($roleSelected, $roleTypeSelected)
    {
        try {
            $data = [];
            $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=> $roleSelected, 'role_user_type'=> $roleTypeSelected])->first();

            if ($getPreSelectedLabTemplates !== null) {
                $data['invite_labs'] = $getPreSelectedLabTemplates->invite_labs;
                $data['invite_challenges'] = $getPreSelectedLabTemplates->invite_challenges;
            }

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
