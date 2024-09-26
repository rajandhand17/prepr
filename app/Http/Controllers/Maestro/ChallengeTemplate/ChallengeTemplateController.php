<?php

namespace App\Http\Controllers\Maestro\ChallengeTemplate;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\ChallengeTemplate;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\ChallengeTemplateService;
use App\Services\Maestro\UserService;
use App\Traits\Maestro\ChallengeTemplate\ChallengeTemplateTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ChallengeTemplateController extends Controller
{
    use ChallengeTemplateTrait;
    protected $challengeTemplateService;
    protected $challengeService;

    public function __construct(ChallengeTemplateService $challengeTemplateService, ChallengeService $challengeService)
    {
        $this->middleware('auth-check');
        $this->challengeTemplateService = $challengeTemplateService;
        $this->challengeService = $challengeService;
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $challengeTemplateInfo = $this->getChallengeTemplate();
            if (!empty($challengeTemplateInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($challengeTemplateInfo)
                        ->editColumn('status', static function (ChallengeTemplate $challengeTemplateInfo) {
                            switch ($challengeTemplateInfo->status) {
                                case '0':
                                    $html = "<span class='badge badge-success'>Draft</span>";
                                    break;
                                case '1':
                                    $html = "<span class='badge badge-success'>Published</span>";
                                    break;
                                case '2':
                                    $html = "<span class='badge badge-success'>Archive</span>";
                                    break;
                            }

                            return $html;
                        })->editColumn('privacy', static function (ChallengeTemplate $challengeTemplateInfo) {
                            switch ($challengeTemplateInfo->privacy) {
                                case '0':
                                    $html = 'Public';
                                    break;
                                case '1':
                                    $html = 'Private';
                                    break;
                            }

                            return $html;
                        })->editColumn('status', static function (ChallengeTemplate $challengeTemplateInfo) {
                            switch ($challengeTemplateInfo->is_open) {
                                case '0':
                                    $status = 'Open';
                                    break;
                                case '1':
                                    $status = 'Close';
                                    break;
                                case '2':
                                    $status = 'Completed';
                                    break;
                            }

                            return $status;
                        })->editColumn('username', static function (ChallengeTemplate $challengeTemplateInfo) {
                            $user = UserService::getUserById($challengeTemplateInfo->user_id);

                            return $user->username;
                        })
                        ->addColumn('action', static function (ChallengeTemplate $challengeTemplateInfo) {
                            return '<a href="javascript:void(0)" onclick="deleteChallengeTemplate(\''.route('challenge-template.destroy', ['challenge_template' => $challengeTemplateInfo->id]).'\')"> <i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['status', 'category', 'username', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'CHALLENGE TITLE', 'width' => '5%'],
                ['data' => 'username', 'name' => 'username', 'title' => 'USERNAME', 'width' => '10%', 'searchable' => false],
                ['data' => 'status', 'name' => 'status', 'title' => 'STATUS', 'width' => '10%'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'PUBLISHED', 'width' => '10%'],
                ['data' => 'action', 'name' => 'action', 'title' => 'ACTION', 'width' => '10%'],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.challenge-template.index', compact('html'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function destroy(string $id)
    {
        try {
            if (!$this->getChallengeTemplateById($id)) {
                return response()->json(['success' =>'false', 'message'=>'This challenge does not exists in the database.']);
            }
            if ($this->deleteChallengeTemplateById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Challenge Template deleted successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function clone($slug)
    {
        try {
            $checkChallengeBasedOnSlug = $this->getChallengeBasedOnSlug($slug);
            if (!$checkChallengeBasedOnSlug) {
                return response()->json(['success' =>'false', 'message'=>'This challenge does not exists in the database.']);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return response()->json(['status' => 'fail', 'message' => 'Preferred Organization not found']);
            }
            if ($checkChallengeBasedOnSlug->is_accessible == '0') {
                return response()->json(['status' => 'fail', 'message' => 'Sorry, this Challenge is not accessible with your existing plan.']);
            }
            if ($checkChallengeBasedOnSlug->is_pre_built == '1') {
                return response()->json(['status' => 'fail', 'message' => 'This Challenge already added in Challenge Template']);
            }
            $addChallengeTemplate = $this->createChallengeTemplate($checkChallengeBasedOnSlug->id);
            if ($addChallengeTemplate != false) {
                return response()->json(['status' => 'success', 'message' => 'Challenge added successfully in challenge template.']);
            }

            return response()->json(['status' => 'fail', 'message' => 'Oops! The creating Challenge template has failed.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
