<?php

namespace App\Http\Controllers\Maestro\ChallengeTemplate;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manage\ChallengeTemplate\ChallengeTemplateResource;
use App\Models\ChallengeTemplate;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\ChallengeTemplateService;
use App\Services\Maestro\User\UserService;
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
        $this->middleware('web');
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
                ['data' => 'username', 'name' => 'username', 'title' => 'USERNAME', 'width' => '10%'],
                ['data' => 'status', 'name' => 'status', 'title' => 'STATUS', 'width' => '10%'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'PUBLISHED', 'width' => '10%'],
                ['data' => 'action', 'name' => 'action', 'title' => 'ACTION', 'width' => '10%'],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.challengeTemplate.index', compact('html'));
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

    public function addChallengeToTemplate($slug)
    {
        try {
            $checkComponentBasedOnSlug = $this->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $checkChallengeTemplate = $this->challengeTemplateRepository->getCheckChallengeUuid($checkComponentBasedOnSlug->uuid);
            if ($checkChallengeTemplate) {
                return $this->sendError(__('responses.challenge_already_cloned'), 422);
            }

            $addChallengeTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($checkComponentBasedOnSlug->id);
            if ($addChallengeTemplate != false) {
                return $this->sendResponse(ChallengeTemplateResource::make($addChallengeTemplate), __('responses.challenge_add_template_success'), 200);
            }

            return $this->sendError(__('responses.challenge_clone_failed'), 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
