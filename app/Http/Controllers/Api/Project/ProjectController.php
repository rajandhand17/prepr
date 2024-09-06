<?php

namespace App\Http\Controllers\Api\Project;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Project\AddAdditionalInfoProjectRequest;
use App\Http\Requests\Project\AddLinksProjectRequest;
use App\Http\Requests\Project\AddProjectAssessmentRequest;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\DeleteProjectMediaRequest;
use App\Http\Requests\Project\FileUploadRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\Project\AssessedProjectResource;
use App\Http\Resources\Project\ProjectAdditionalInfoResource;
use App\Http\Resources\Project\ProjectExternalLinkResource;
use App\Http\Resources\Project\ProjectHistoryResource;
use App\Http\Resources\Project\ProjectRequirementResource;
use App\Http\Resources\Project\ProjectResource;
use App\Repositories\Api\Project\ProjectRepository;
use App\Services\ChallengeAssessmentUserService;
use App\Services\LastVisitedActivityModuleService;
use App\Services\Manage\ChallengeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends AppBaseController
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function index(Request $request)
    {
        try {
            if (!in_array($request->type, ['my', 'team', 'invites', 'favourite', 'assessed', 'pending_assessment'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }
            if ($request->access_level) {
                if (!in_array($request->access_level, ['team_leader', 'editor', 'viewer'])) {
                    return $this->sendError(__('responses.role_not_exists'), 422);
                }
            }
            switch ($request->type) {
                case 'my':
                    $getProjectIds = $this->projectRepository->getMyProjectIds(auth()->user()->id);
                    break;

                case 'team':
                    $getProjectIds = $this->projectRepository->getAcceptedInvitesProjectIds(auth()->user());
                    break;

                case 'invites':
                    $getProjectIds = $this->projectRepository->getPendingInvitesProjectIds(auth()->user());
                    break;

                case 'favourite':
                    $getProjectIds = $this->projectRepository->getFavouriteProjectIds(auth()->user()->id);
                    break;

                case 'assessed':
                    $getProjectIds = $this->projectRepository->getAssessedProjectIds(auth()->user());
                    break;

                case 'pending_assessment':
                    $getProjectIds = $this->projectRepository->getPendingProjectIds(auth()->user());
                    break;
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 402);
                    break;
            }
            if ($getProjectIds) {
                $project = $this->projectRepository->getProjectList($getProjectIds, $request);
                if ($project !== false) {
                    $response = [
                        'total_count'           => $project->total(),
                        'per_page'              => $project->perPage(),
                        'count'                 => $project->count(),
                        'current_page'          => $project->currentPage(),
                        'total_pages'           => $project->lastPage(),
                        'pending_invites'       => $this->projectRepository->getPendingInvitesProjectIds(auth()->user())->count(),
                        'pending_assessments'   => $this->projectRepository->getPendingProjectIds(auth()->user())->count(),
                        'list'                  => ProjectResource::collection($project),
                    ];

                    return $this->sendResponse($response, __('responses.found_projects_list'));
                }
            }

            return $this->sendError(__('responses.not_found_projects_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($checkProjectSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkProjectTitle = $this->projectRepository->checkNameExistsOrNot($title);
            if ($checkProjectTitle == false) {
                return $this->sendResponse([], __('responses.project_name_available'));
            }

            return $this->sendError(__('responses.project_name_not_available'), 403);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateProjectRequest $request)
    {
        try {
            $checkChallenge = ChallengeService::getChallengeBasedOnUUID($request->challenge_id);
            if ($checkChallenge) {
                $challengeStatus = ($checkChallenge->status == '1' && $checkChallenge->is_open == '0');
                if ($challengeStatus) {
                    $checkChallengeTimelineType = $checkChallenge->challenge_timelines->timeline_type;
                    if ($checkChallengeTimelineType == '1') {
                        if (!$checkChallenge->challenge_timelines->application_deadline_date > Carbon::now()->toDateTimeString()) {
                            return $this->sendError(__('responses.challenge_timeline_fail'), 403);
                        }
                    }
                } else {
                    return $this->sendError(__('responses.challenge_status_fail'), 403);
                }
            } else {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            // Checking user has already created project with challenge or not based on user and challenge id's
            $checkProjectCreatedWithChallenge = $this->projectRepository->checkProjectCreatedWithChallenge($checkChallenge->id);
            if ($checkProjectCreatedWithChallenge) {
                return $this->sendError(__('responses.project_already_created'), 403);
            }

            $upload_project_cover_media = config('site-settings.default_project_cover_image');
            if ($request->cover_media != null) {
                if ($request->media_type == 'image') {
                    $uploaded_cover_media = $this->projectRepository->uploadCoverImage($request->cover_media);
                    if (!$uploaded_cover_media) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_media = $request->cover_media;
                }

                $upload_project_cover_media = $uploaded_cover_media;
            }
            $createProject = $this->projectRepository->createProject($request, $upload_project_cover_media);
            if ($createProject) {
                return $this->sendResponse(ProjectResource::make($createProject), __('responses.project_stored_success'), 200);
            }

            return $this->sendError(__('responses.project_stored_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectPitchTask($slug, Request $request)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $checkChallenge = ChallengeService::getChallengeBasedOnId($checkProjectExistsOrNot->challenge_id);
            if ($checkChallenge == false) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }
            $addPitchTask = $this->projectRepository->projectPitchTask($checkProjectExistsOrNot->id, $request);

            if ($addPitchTask) {
                return $this->sendResponse(ProjectResource::make($checkProjectExistsOrNot), __('responses.project_pitch_stored_success'), 200);
            }

            return $this->sendError(__('responses.project_pitch_stored_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fileUpload($slug, FileUploadRequest $request)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $addProjectFiles = $this->projectRepository->projectProjectFile($checkProjectExistsOrNot->id, $request);

            if ($addProjectFiles) {
                return $this->sendResponse(ProjectResource::make($checkProjectExistsOrNot), __('responses.project_file_stored_success'), 200);
            }

            return $this->sendError(__('responses.project_file_stored_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $project = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($project) {
                if (auth('api')->check()) {
                    $userId = auth('api')->user()->id;
                    if ($userId == $project->user_id) {
                        // For last visited activity tracking
                        $moduleType = config('constants.module_type.projects');
                        LastVisitedActivityModuleService::lastVisitedActivityModule($project->id, $userId, $moduleType);
                    }
                }

                if (!auth('api')->check() && $project->privacy == '1') {
                    return $this->sendError(__('responses.project_set_private'), 403);
                }

                return $this->sendResponse(ProjectResource::make($project), __('responses.found_project_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_project_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateProjectRequest $request)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $checkChallenge = ChallengeService::getChallengeBasedOnId($checkProjectExistsOrNot->challenge_id);
            if ($checkChallenge) {
                $challengeStatus = ($checkChallenge->status == '1' && $checkChallenge->is_open == '0');
                if ($challengeStatus) {
                    $checkChallengeTimelineType = $checkChallenge->challenge_timelines->timeline_type;
                    if ($checkChallengeTimelineType == '1') {
                        if (!$checkChallenge->challenge_timelines->application_deadline_date > Carbon::now()->toDateTimeString()) {
                            return $this->sendError(__('responses.challenge_timeline_fail'), 403);
                        } else {
                        }
                    }
                } else {
                    return $this->sendError(__('responses.challenge_status_fail'), 403);
                }
            } else {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            $update_cover_image = str_replace(config('site-settings.aws_url'), '', $checkProjectExistsOrNot->media);
            if ($request->has('media_type') && $request->media_type != 'none') {
                if ($request->media_type == 'image') {
                    $uploaded_cover_media = $this->projectRepository->uploadCoverImage($request->cover_media);
                    if (!$uploaded_cover_media) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                    $update_cover_image = $uploaded_cover_media;
                } elseif ($request->media_type == 'embedded') {
                    $update_cover_image = $request->cover_media;
                }
            }

            $updateProject = $this->projectRepository->updateProject($slug, $request, $update_cover_image);
            if ($updateProject != false) {
                return $this->sendResponse(ProjectResource::make($updateProject), __('responses.project_update_successfully'), 200);
            }

            return $this->sendError(__('responses.project_not_update'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectExternalLinks(AddLinksProjectRequest $request, $slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }
            $addLinks = $this->projectRepository->addUpdateExternalLink($request, $checkProjectExistsOrNot->id);
            if ($addLinks) {
                return $this->sendResponse(ProjectExternalLinkResource::collection($checkProjectExistsOrNot->external_links), __('responses.add_external_links_success'), 200);
            }

            return $this->sendResponse(__('responses.add_external_links_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectAdditionalInfo(AddAdditionalInfoProjectRequest $request, $slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }
            $addAdditionalInfo = $this->projectRepository->addUpdateAdditionalInfo($request, $checkProjectExistsOrNot->id);
            if ($addAdditionalInfo) {
                return $this->sendResponse(ProjectAdditionalInfoResource::make($checkProjectExistsOrNot->getProjectAdditionalInfo), __('responses.add_additional_success'), 200);
            }

            return $this->sendResponse(__('responses.add_additional_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectRequirements($slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $getProjectChallengeRequirement = $this->projectRepository->projectRequirements($checkProjectExistsOrNot);
            if ($getProjectChallengeRequirement) {
                return $this->sendResponse(ProjectRequirementResource::make($checkProjectExistsOrNot), __('responses.project_requirement_found'), 200);
            }

            return $this->sendError(__('responses.project_not_requirement_found'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $deleteProject = $this->projectRepository->deleteProject($checkProjectExistsOrNot->id);
            if ($deleteProject) {
                return $this->sendResponse(null, __('responses.project_delete'));
            }

            return $this->sendError(__('responses.project_not_delete'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function submitProject(Request $request, $slug)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectSlugExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            if (in_array($checkProjectSlugExistsOrNot->is_submitted, ['1', '2'])) {
                return $this->sendError(__('responses.project_already_submitted'), 400);
            }

            $checkProjectRequirementCompleted = $this->projectRepository->checkProjectRequirementCompleted($checkProjectSlugExistsOrNot);
            if (!$checkProjectRequirementCompleted) {
                return $this->sendError(__('responses.project_requirements_pending'), 400);
            }

            $checkLateSubmission = $this->projectRepository->checkSubmisstionDate($checkProjectSlugExistsOrNot);
            $submitProject = $this->projectRepository->submitProject($checkProjectSlugExistsOrNot, $checkLateSubmission, $request);

            if ($submitProject === 'no') {
                return $this->sendError(__('responses.late_submission_reason_required'), 400);
            }
            if ($submitProject === true) {
                return $this->sendResponse(ProjectResource::make($checkProjectSlugExistsOrNot), __('responses.project_submitted'), 200);
            }

            return $this->sendError(__('responses.project_not_submitted'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $fetchProject = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($fetchProject) {
                $getColumnNameValue = $this->projectRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }

                $checkActivity = $this->projectRepository->checkSocialActivity($fetchProject->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity == true) {
                    return $this->sendError(__('responses.already_'.$action.'_project'), 400);
                }

                $captureActivity = $this->projectRepository->captureSocialActivity($fetchProject->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($captureActivity) {
                    return $this->sendResponse([], __('responses.'.$action.'_project_successfully'));
                }
            }

            return $this->sendError(__('responses.found_not_project_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function viewAssessedProject($slug)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectSlugExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            if ($checkProjectSlugExistsOrNot->getProjectAssessment) {
                return $this->sendResponse(AssessedProjectResource::make($checkProjectSlugExistsOrNot), __('responses.project_assessment_retrived'), 200);
            }

            return $this->sendError(__('responses.project_assessment_not_retrived'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function captureAssessmentProject($slug, AddProjectAssessmentRequest $request)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectSlugExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $checkAssessmentChallenges = $this->projectRepository->checkAssessmentChallenges(auth()->user());
            if ($checkAssessmentChallenges->contains($checkProjectSlugExistsOrNot->challenge_id) == false) {
                return $this->sendError(__('responses.project_not_allowed_assessment'), 403);
            }

            $captureProjectAssessment = $this->projectRepository->captureProjectAssessment($checkProjectSlugExistsOrNot, auth()->user(), $request);
            if ($captureProjectAssessment) {
                $fetchProjectAssessment = ChallengeAssessmentUserService::getProjectAssessmentData($checkProjectSlugExistsOrNot, auth()->user()->id);
                switch ($fetchProjectAssessment['assessment_status']) {
                    case 'published':
                        $responseMessage = __('responses.project_assessment_submitted');
                        break;
                    case 'draft':
                        $responseMessage = __('responses.project_assessment_draft');
                        break;
                }

                return $this->sendResponse(AssessedProjectResource::make($checkProjectSlugExistsOrNot), $responseMessage, 200);
            }

            return $this->sendError(__('responses.project_not_assessment_submitted'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function captureAIAssessmentProject($slug, Request $request)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectSlugExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $checkAssessmentChallenges = $this->projectRepository->checkAssessmentChallenges(auth()->user());
            if ($checkAssessmentChallenges->contains($checkProjectSlugExistsOrNot->challenge_id) == false) {
                return $this->sendError(__('responses.project_not_allowed_assessment'), 403);
            }

            $captureProjectAssessment = $this->projectRepository->captureProjectAIAssessment($checkProjectSlugExistsOrNot, auth()->user(), $request);

            if ($captureProjectAssessment) {
                $responseMessage = __('responses.project_assessment_submitted');

                return $this->sendResponse(null, $responseMessage, 200);
            }

            return $this->sendError(__('responses.project_not_assessment_submitted'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in captureAIAssessmentProject in ProjectController.php: '.$e->getMessage());

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function scoreAIAssessmentProject($slug, Request $request)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectSlugExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $assessProjectAI = $this->projectRepository->assessProjectAI($request);

            if ($assessProjectAI) {
                ChallengeAssessmentUserService::getProjectAssessmentData($checkProjectSlugExistsOrNot, auth()->user()->id);
                $responseMessage = __('responses.project_assessment_received');

                return $this->sendResponse(null, $responseMessage, 200);
            }

            return $this->sendError(__('responses.project_not_assessment_submitted'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in scoreAIAssessmentProject in ProjectController.php: '.$e->getMessage());

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteAssessmentProject($slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $checkChallengeProjectAssessment = $this->projectRepository->checkChallengeProjectAssessment($checkProjectExistsOrNot->id, auth()->user());
            if ($checkChallengeProjectAssessment == false) {
                return $this->sendError(__('responses.project_assessment_not_done_by_you'), 403);
            }

            $deleteProjectAssessment = $this->projectRepository->deleteChallengeProjectAssessment($checkProjectExistsOrNot->id, auth()->user());
            if ($deleteProjectAssessment) {
                return $this->sendResponse([], __('responses.project_assessment_deleted'), 200);
            }

            return $this->sendError(__('responses.project_assessment_not_delete'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteMedia(DeleteProjectMediaRequest $request, $slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $deleteProjectMedia = $this->projectRepository->deleteProjectMedia($request, $checkProjectExistsOrNot->id);
            if ($deleteProjectMedia) {
                return $this->sendResponse(null, __('responses.project_media_delete'));
            }

            return $this->sendError(__('responses.project_media_not_delete'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectHistory($slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $fetchProjectHistory = $this->projectRepository->fetchProjectHistory($checkProjectExistsOrNot->id);
            if ($fetchProjectHistory) {
                return $this->sendResponse(ProjectHistoryResource::collection($fetchProjectHistory), __('responses.project_history_retrived'), 200);
            }

            return $this->sendError(__('responses.project_history_not_retrived'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function joinProject($slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            if ($checkProjectExistsOrNot->recruiting_status == '1') {
                return $this->sendError(__('responses.project_join_not_allowed'), 404);
            }

            $userEmail = auth()->user()->email;
            $checkProjectJoinedStatus = $this->projectRepository->checkProjectJoinedStatus($checkProjectExistsOrNot->id, $userEmail);
            if ($checkProjectJoinedStatus != false) {
                if ($checkProjectJoinedStatus->invite_status == '0') {
                    return $this->sendError(__('responses.project_join_invited'), 404);
                }

                if ($checkProjectJoinedStatus->invite_status == '1') {
                    return $this->sendError(__('responses.project_join_member_already'), 404);
                }

                if ($checkProjectJoinedStatus->invite_status == '2') {
                    return $this->sendError(__('responses.project_join_request_already_sent'), 404);
                }
            }

            $joinProject = $this->projectRepository->joinProject($checkProjectExistsOrNot->id, $userEmail);
            if ($joinProject) {
                return $this->sendResponse(null, __('responses.project_join_successfully'), 200);
            }

            return $this->sendError(__('responses.project_joined_failed'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unJoinProject($slug)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if (!$checkProjectExistsOrNot) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $userEmail = auth()->user()->email;
            $checkProjectJoinedStatus = $this->projectRepository->checkProjectJoinedStatus($checkProjectExistsOrNot->id, $userEmail);
            if ($checkProjectJoinedStatus == false) {
                return $this->sendError(__('responses.project_unjoin_request_already'), 404);
            }

            if ($checkProjectJoinedStatus != false) {
                if ($checkProjectJoinedStatus->invite_status == '0') {
                    return $this->sendError(__('responses.project_join_invited'), 404);
                }
            }

            $unJoinProject = $this->projectRepository->unJoinProject($checkProjectExistsOrNot->id, $userEmail);
            if ($unJoinProject) {
                return $this->sendResponse(null, __('responses.project_unjoined_successfully'), 200);
            }

            return $this->sendError(__('responses.project_unjoined_failed'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
