<?php

namespace App\Http\Controllers\Maestro\AutoCreateTemplate;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Traits\Maestro\AutoCreateTemplate\AutoCreateTemplateTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class AutoCreateTemplateController extends Controller
{
    use AutoCreateTemplateTrait;

    public function index(Builder $builder)
    {
        try {
            $roles = $this->getRole();

            return view('maestro.autocreatetemplate.index1', compact('roles'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function cloneModule(Request $request)
    {
        try {
            $clone = $this->cloneModules($request);
            if ($clone) {
                return redirect()->back()->with(['success' => 'Clone module successfully']);
            }

            return redirect()->back()->with(['error' =>'Clone module failed']);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }

    public function getModuleList(Request $request)
    {
        try {
            $getModuleList = $this->fetchModuleList($request);
            if ($getModuleList) {
                return $getModuleList;
            }

            return redirect()->back()->with(['error' =>'Module List Not Found']);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }

    public function getPreSelectList(Request $request)
    {
        try {
            $getPreSelectList = $this->fetchPreSelectList($request);
            if ($getPreSelectList) {
                return $getPreSelectList;
            }

            return redirect()->back()->with(['error' =>'Pre Select List Not Found']);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' =>$e->getMessage()]);
        }
    }
}
