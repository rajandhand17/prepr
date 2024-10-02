<?php

namespace App\Http\Controllers\Maestro\AutoCreateTemplate;

use App\Http\Controllers\Controller;
use App\Services\Maestro\AutoCreateTemplates\AutoCreateTemplatesService;
use App\Traits\Maestro\AutoCreateTemplate\AutoCreateTemplateTrait;
use Exception;
use Illuminate\Http\Request;

class AutoCreateTemplateController extends Controller
{
    use AutoCreateTemplateTrait;

    public function index()
    {
        try {
            return view('maestro.autocreatetemplate.index');
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function createUpdate(Request $request)
    {
        try {
            if ($this->createUpdateAutoTemplate($request)) {
                return redirect()->back()->with(['success' => 'Auto-create template process completed successfully.']);
            }

            return redirect()->back()->with(['error' => 'Auto-create template process fail please select role and try again.']);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function getPreSelectLabList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::getPreSelectLabList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function getPreSelectedChallengeList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::getPreSelectedChallengeList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function fetchLabList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::fetchLabList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function fetchChallengeList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::fetchChallengeList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function fetchChallengeGroupList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::fetchChallengeGroupList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function fetchLabGroupList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::fetchLabGroupList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function getPreSelectLabGroupList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::getPreSelectLabGroupList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }

    public function getPreSelectChallengeGroupList(Request $request)
    {
        try {
            return AutoCreateTemplatesService::getPreSelectChallengeGroupList($request);
        } catch (Exception $e) {
            return  response()->json([]);
        }
    }
}
