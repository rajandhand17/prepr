<?php

namespace App\Services\Maestro\AutoCreateTemplates;

use App\Helpers\UtilityHelper;
use App\Models\AutoCreateTemplate;
use App\Models\Lab;
use App\Models\Role;
use App\Services\Maestro\ChallengePathService;
use App\Services\Maestro\ChallengeTemplateService;
use App\Services\Maestro\LabMarketplaceService;
use App\Services\Maestro\LabProgramService;
use App\Services\Maestro\ProjectService;
use Exception;

class AutoCreateTemplatesService
{
    public static function getList($request)
    {
        try {
            $getRoleType = Role::where('name', 'like', '%'.$request->role_selected.'%')->first()->id;

            $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=>$getRoleType, 'user_type'=>'4'])->pluck($request->plucked);
            // Rajan why we are picking these from template table not from the lab table? in maestro we were fatching from the lab table.
            switch ($request->plucked) {
                case 'lab_template_id':
                    $getList = LabMarketplaceService::getList($getPreSelectedLabTemplates, $request->language);
                    break;
                case 'challenge_template_id':
                    $getList = ChallengeTemplateService::getList($getPreSelectedLabTemplates, $request->language);
                    break;
                case 'project_id':
                    $getList = ProjectService::getList($getPreSelectedLabTemplates, $request->language);
                    break;
                case 'lab_program_id':
                    $getList = LabProgramService::getList($getPreSelectedLabTemplates, $request->language);
                    break;
                case 'challenge_path_id':
                    $getList = ChallengePathService::getList($getPreSelectedLabTemplates, $request->language);
                    break;
                default:
                    $getList = '';
                    break;
            }
            if ($getList !== '') {
                $request->language = 'en';
                $count = 0;
                $data = [];
                $labsr = [];
                foreach ($getList as $key => $title) {
                    $labsr[$count]['id'] = $key;
                    $labsr[$count]['text'] = $title;
                    $count++;
                }
                $data['result'] = $labsr ?? [];

                return $data;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function fetchModuleList($request)
    {
        try {
            $modules = [];
            $data = [];
            switch ($request->module) {
                case 'lab_template':
                    $modules = LabMarketplaceService::getLabMarketplaceList($request);
                    break;
                case 'lab_program':
                    $modules = LabProgramService::getLabProgramList($request);
                    break;
                case 'challenge_template':
                    $modules = ChallengeTemplateService::getChallengesTemplateList($request);
                    break;
                case 'challenge_path':
                    $modules = ChallengePathService::getChallengePathList($request);
                    break;
            }

            $count = 0;
            if (!empty($modules)) {
                foreach ($modules as $key => $title) {
                    $data[$count]['id'] = $key;
                    $data[$count]['title'] = $title;
                    $count++;
                }
            }

            return  response()->json($data);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneModule($request)
    {
        try {
            $labTemplateIds = [];
            $challengeTemplateId = [];
            $labProgramId = [];
            $challengePathId = [];
            if (isset($request->selected_lab_ids) && !empty($request->selected_lab_ids)) {
                $labTemplateIds = explode(',', $request->selected_lab_ids);
            }
            if (isset($request->selected_challenge_ids) && !empty($request->selected_challenge_ids)) {
                $challengeTemplateId = explode(',', $request->selected_challenge_ids);
            }
            if (isset($request->selected_group_challenge_ids) && !empty($request->selected_group_challenge_ids)) {
                $challengePathId = explode(',', $request->selected_group_challenge_ids);
            }
            if (isset($request->selected_group_lab_ids) && !empty($request->selected_group_lab_ids)) {
                $labProgramId = explode(',', $request->selected_group_lab_ids);
            }
            $arrays = [$labTemplateIds, $challengeTemplateId, $labProgramId, $challengePathId];
            $maxArray = array_reduce($arrays, function ($max, $array) {
                return count($array) > count($max) ? $array : $max;
            }, []);
            $maxCount = count($maxArray);
            $autoCreateTemplate = AutoCreateTemplate::where(['role_type'=>$request->selected_role, 'user_type'=>$request->role_user_type_slected])->delete();
            for ($i = 0; $i < $maxCount; $i++) {
                $autoCreateTemplate = new AutoCreateTemplate();
                $autoCreateTemplate->role_type = $request->selected_role;
                $autoCreateTemplate->user_type = $request->role_user_type_slected;
                $autoCreateTemplate->lab_template_id = isset($labTemplateIds[$i]) ? $labTemplateIds[$i] : null;
                $autoCreateTemplate->challenge_template_id = isset($challengeTemplateId[$i]) ? $challengeTemplateId[$i] : null;
                $autoCreateTemplate->lab_program_id = isset($labProgramId[$i]) ? $labProgramId[$i] : null;
                $autoCreateTemplate->challenge_path_id = isset($challengePathId[$i]) ? $challengePathId[$i] : null;
                $autoCreateTemplate->invite_labs = $request->invite_lab;
                $autoCreateTemplate->invite_challenges = $request->invite_challenge;
                if (!$autoCreateTemplate->save()) {
                    return false;
                }
            }

            return true;
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

    public static function fetchPreSelectList($request)
    {
        try {
            $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'user_type'=> $request->role_type_selected])->pluck('lab_template_id')->first();
            $explodeLabIdsArray = explode(',', $getPreSelectedLabTemplates);
            $data = [];
            dd($explodeLabIdsArray);
            $labs = Lab::whereIn('id', $explodeLabIdsArray)->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            $count = 0;
            dd($labs);
            foreach ($labs as $key => $title) {
                $labsr[$count]['id'] = $key;
                $labsr[$count]['text'] = $title;
                $count++;
            }
            dd($labsr);
            $inviteInfo = self::getInviteUserInfo($request->role_selected, $request->role_type_selected);

            $data['result'] = $labsr ?? [];
            $data['invite_info'] = $inviteInfo ?? [];
            dd($data);

            return  response()->json($data);
        } catch (Exception $e) {
            dd($e);
            UtilityHelper::logError($e);

            return false;
        }
    }
}
