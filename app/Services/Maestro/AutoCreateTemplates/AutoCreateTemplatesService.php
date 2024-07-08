<?php

namespace App\Services\Maestro\AutoCreateTemplates;

use App\Models\AutoCreateTemplate;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChallengeTemplate;
use App\Models\Lab;
use App\Models\LabMarketplace;
use App\Models\LabProgram;
use App\Models\Language;
use App\Models\Project;
use App\Models\Role;
use Exception;

class AutoCreateTemplatesService
{

    public static function getList($request)
    {
        try {
            $getRoleType=Role::where('name','like', '%'.$request->role_selected.'%')->first()->id;

            $getPreSelectedLabTemplates = AutoCreateTemplate::where(['role_type'=>$getRoleType, 'user_type'=>'4'])->pluck($request->plucked);
            switch ($request->plucked){
                case 'lab_template_id';
                $model=LabMarketplace::class;
                break;
                case 'challenge_template_id';
                $model=ChallengeTemplate::class;
                break;
                case 'project_id';
                $model=Project::class;
                break;
                case 'lab_program_id';
                $model=LabProgram::class;
                break;
                case 'challenge_path_id';
                $model=ChallengePath::class;
                break;
                default:
                    $model='';
                    break;
            }
            if($model!==''){
                $request->language='en';
                $getList=$model::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id');
                $count = 0;
                $data = [];
                $labsr=[];
                foreach ($getList as $key => $title) {
                    $labsr[$count]['id'] = $key;
                    $labsr[$count]['text'] = $title;
                    $count++;
                }
                $data['result'] = $labsr ?? [];
                return $data;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }

    public static function fetchModuleList($request)
    {
        $module=Lab::class;
//        $searched = $request->search;
//        $module=$request->module;
        try {
            $data = [];
            if (!empty($searched)) {
                $moduls = $module::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language)->where('title', 'like', '%' . $searched . '%')->pluck('title', 'id')->toArray();
            } else {
                $moduls = $module::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language)->pluck('title', 'id')->toArray();
            }
            $count = 0;
            foreach ($moduls as $key => $title) {
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

    public static function cloneModule($request)
    {
        try {
            $labIds = explode(',', $request->selected_lab_ids);
            $challengeIds = explode(',',$request->selected_challenge_ids);
            $groupChallengeIds = explode(',', $request->selected_group_challenge_ids);
            $groupChallengeIds = explode(',', $request->selected_group_lab_ids);
            $autoCreateTemplate=AutoCreateTemplatesService::where(['role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected])->first();
            if(!empty($autoCreateTemplate)){

            }else{

            }
            if ($request->sdc_lab== 'true') {
                AutoCreateTemplate::updateOrCreate(
                    ['role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected],
                    ['language'=>$request->selected_language,'role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected,'lab_id'=> $request->selected_lab_ids, 'lab_group_id'=> $request->selected_group_lab_ids,'invite_labs'=>$request->invite_lab]
                );
            }
            if ($request->sdc_lab== 'false') {
                AutoCreateTemplate::updateOrCreate(
                    ['role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected],
                    ['language'=>$request->selected_language,'role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected,'lab_id'=> null, 'lab_group_id'=> null,'invite_labs'=> '0']
                );
            }
            if ($request->sdc_challenge== 'true') {
                AutoCreateTemplate::updateOrCreate(
                    ['role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected],
                    ['language'=>$request->selected_language,'role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected,'challenge_id'=> $request->selected_challenge_ids,'challenge_group_id'=> $request->selected_group_challenge_ids,'invite_challenges'=>$request->invite_challenge]
                );
            }
            if ($request->sdc_challenge== 'false') {
                AutoCreateTemplate::updateOrCreate(
                    ['role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected],
                    ['language'=>$request->selected_language,'role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected,'challenge_id'=> null,'challenge_group_id'=> null,'invite_challenges'=> '0']
                );
            }
            if ($request->sdc_lab== 'false' && $request->sdc_challenge== 'false') {
                AutoCreateTemplate::updateOrCreate(
                    ['role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected],
                    ['language'=>$request->selected_language,'role_type'=> $request->selected_role,'role_user_type'=> $request->role_user_type_slected,'lab_id'=> null, 'lab_group_id'=> null,'invite_labs'=>'0','challenge_id'=> null,'challenge_group_id'=> null,'invite_challenges'=> '0']
                );
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
