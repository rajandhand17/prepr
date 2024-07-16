<?php

namespace App\Http\Controllers\Maestro\AutoCreateTemplate;

use App\Http\Controllers\Controller;
use App\Models\AutoCreateTemplate;
use App\Models\AutoCreateTemplates;
use App\Models\ChallengePath;
use App\Models\Group;
use App\Models\LabProgram;
use App\Traits\Maestro\AutoCreateTemplate\AutoCreateTemplateTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class AutoCreateTemplateController extends Controller
{
    use AutoCreateTemplateTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder)
    {
        try {
            $roles=$this->getRole();
            return view('maestro.autocreatetemplate.index', compact('roles'));
        }catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function getPreSelectLabList(Request $request)
    {
        try {
            $getAllAutoTemplateList=$this->getPreSelectLabLists($request);
            if($getAllAutoTemplateList){
                return $getAllAutoTemplateList;
            }
            return redirect()->back()->with(['error' =>'Unable to get all list']);
        }catch (\Exception $e){
            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }

    public function getPreSelectedChallengeList(Request $request)
    {
        try {
            $getAllAutoTemplateList=$this->getPreSelectedChallengeLists($request);
            if($getAllAutoTemplateList){
                return $getAllAutoTemplateList;
            }
            return redirect()->back()->with(['error' =>'Unable to get all list']);
        }catch (\Exception $e){
            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }

    public function getPreSelectLabGroupList(Request $request)
    {
        try {
            $getPreSelectedLabGroupTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'role_user_type'=> $request->role_type_selected])->pluck('lab_group_id')->first();
            $explodeLabGroupIdsArray= explode(',', $getPreSelectedLabGroupTemplates);
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
        }catch (\Exception $e){
            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }
    public function getPreSelectChallengeGroupList(Request $request)
    {
        try {
            dd($request);
            $getPreSelectedChallengeGroupTemplates = AutoCreateTemplate::where(['role_type'=> $request->role_selected, 'role_user_type'=> $request->role_type_selected])->pluck('challenge_group_id')->first();
            $explodeChallengeGroupIdsArray= explode(',', $getPreSelectedChallengeGroupTemplates);
            $data = [];
            $challengeGroups = ChallengePath::whereIn('id', $explodeChallengeGroupIdsArray)->where('privacy', 'public')->where('type', '=', 'challenge')->where('language', $request->language)->orderBy('id', 'DESC')->pluck('title', 'id')->toArray();
            $count = 0;
            foreach ($challengeGroups as $key => $title) {
                $challengesr[$count]['id'] = $key;
                $challengesr[$count]['text'] = $title;
                $count++;
            }

            $data['result'] = $challengesr ?? [];

            return  response()->json($data);
        }catch (\Exception $e) {
            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }
    public function cloneModule(Request $request){
        try {
            $clone=$this->cloneModules($request);
            if($clone){
                return redirect()->back()->with(['success' => 'Updated  successfully']);
            }
            return redirect()->back()->with(['error' =>'upload failed']);
        }catch (\Exception $e){
            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }

    public function getModuleList(Request $request)
    {
        try {
            $getModuleList=$this->fetchModuleList($request);
            if($getModuleList){
                return $getModuleList;
            }
        }catch (\Exception $e){
            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }
}
