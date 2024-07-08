<?php

namespace App\Http\Controllers\Maestro\AutoCreateTemplate;

use App\Http\Controllers\Controller;
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

    public function getList(Request $request)
    {
        try {
            $getAllAutoTemplateList=$this->getLists($request);
            if($getAllAutoTemplateList){
                return $getAllAutoTemplateList;
            }
        }catch (\Exception $e){
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
