<?php

namespace App\Services\Maestro\AutoCreateTemplates;

use App\Models\AutoCreateTemplate;
use App\Models\ChallengePath;
use App\Models\ChallengeTemplate;
use App\Models\LabMarketplace;
use App\Models\LabProgram;
use App\Models\Project;
use App\Models\Role;
use Exception;

class AutoCreateTemplatesService
{
    public static function getList($request)
    {
        try {
            $getRoleType = Role::where('name', 'like', '%'.$request->role_selected.'%')->first()->id;

            $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=>$getRoleType, 'user_type'=>'4'])->pluck($request->plucked);
            switch ($request->plucked) {
                case 'lab_template_id':
                    $model = LabMarketplace::class;
                    break;
                case 'challenge_template_id':
                    $model = ChallengeTemplate::class;
                    break;
                case 'project_id':
                    $model = Project::class;
                    break;
                case 'lab_program_id':
                    $model = LabProgram::class;
                    break;
                case 'challenge_path_id':
                    $model = ChallengePath::class;
                    break;
                default:
                    $model = '';
                    break;
            }
            if ($model !== '') {
                $request->language = 'en';
                $getList = $model::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id');
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
            switch ($request->module) {
                case 'lab_template':
                    $module = LabMarketplace::class;
                    break;
                case 'lab_program':
                    $module = LabProgram::class;
                    break;
                case 'challenge_template':
                    $module = ChallengeTemplate::class;
                    break;
                case 'challenge_path':
                    $module = ChallengePath::class;
                    break;
            }
            $searched = $request->search;
            $modules = $module::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language);
            if (!empty($searched)) {
                $modules = $modules->where('title', 'like', '%'.$searched.'%');
            }
            $modules = $modules->pluck('title', 'id');
            $count = 0;
            $data = [];
            if (!empty($module)) {
                foreach ($modules as $key => $title) {
                    $data[$count]['id'] = $key;
                    $data[$count]['title'] = $title;
                    $count++;
                }
            }

            return  response()->json($data);
        } catch (Exception $e) {
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
                $labProgramId = explode(',', $request->selected_group_challenge_ids);
            }
            if (isset($request->selected_group_lab_ids) && !empty($request->selected_group_lab_ids)) {
                $challengePathId = explode(',', $request->selected_group_lab_ids);
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
            return false;
        }
    }

    public static function getInviteUserInfo($roleSelected, $roleTypeSelected)
    {
        $data = [];
        $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=> $roleSelected, 'role_user_type'=> $roleTypeSelected])->first();
        if ($getPreSelectedLabTemplates !== null) {
            $data['invite_labs'] = $getPreSelectedLabTemplates->invite_labs;
            $data['invite_challenges'] = $getPreSelectedLabTemplates->invite_challenges;
        }

        return $data;
    }
}
