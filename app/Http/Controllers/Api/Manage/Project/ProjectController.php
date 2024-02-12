<?php

namespace App\Http\Controllers\Api\Manage\Project;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Project\AddAdditionalInfoProjectRequest;
use App\Http\Requests\Manage\Project\AddLinksProjectRequest;
use App\Http\Requests\Manage\Project\CreateProjectRequest;
use App\Http\Requests\Manage\Project\UpdateProjectRequest;
use App\Http\Resources\Manage\Challenge\ChallengeListNameResource;
use App\Http\Resources\Manage\Lab\LabListNameResource;
use App\Http\Resources\Manage\Project\FavouriteProjectListingResource;
use App\Http\Resources\Manage\Project\InvitedProjectListingResource;
use App\Http\Resources\Manage\Project\MyProjectListingResource;
use App\Http\Resources\Manage\Project\ProjectAdditionalInfoResource;
use App\Http\Resources\Manage\Project\ProjectExternalLinkResource;
use App\Http\Resources\Manage\Project\ProjectFileResource;
use App\Http\Resources\Manage\Project\ProjectRequirementResource;
use App\Http\Resources\Manage\Project\ProjectResource;
use App\Repositories\Api\Manage\Project\ProjectRepository;
use App\Services\Manage\ChallengeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ProjectController extends AppBaseController
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function index($type, Request $request)
    {
        try {
            if (!in_array($type, ['my', 'favourite', 'invited'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }

            switch ($type) {
                case 'my':
                    $getProjectIds = $this->projectRepository->getMyProjectIds(auth()->user()->id);
                    $resourceClass = MyProjectListingResource::class;
                    break;

                case 'favourite':
                    $getProjectIds = $this->projectRepository->getFavouriteProjectIds(auth()->user()->id);
                    $resourceClass = FavouriteProjectListingResource::class;
                    break;

                case 'invited':
                    $getProjectIds = $this->projectRepository->getInvitedProjectIds(auth()->user());
                    $resourceClass = InvitedProjectListingResource::class;
                    break;

                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                    break;
            }

            if (!empty($getProjectIds) && count($getProjectIds) > 0) {
                $project = $this->projectRepository->getProjectList($getProjectIds, $request);
                $projectResource = $resourceClass::collection($project);
                if ($project) {
                    $response = [
                        'total_count'  => $project->total(),
                        'per_page'     => $project->perPage(),
                        'count'        => $project->count(),
                        'current_page' => $project->currentPage(),
                        'total_pages'  => $project->lastPage(),
                        'list'         => $projectResource,
                    ];

                    return $this->sendResponse($response, __('responses.found_projects_list'));
                }
                return $this->sendError(__('responses.not_found_projects_list'), 400);
            }

            return $this->sendError(__('responses.project_list_type'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function challengeList(Request $request)
    {
        try {
            $getProjectChallengeList = $this->projectRepository->getProjectChallenges($request);
            if ($getProjectChallengeList) {
                return $this->sendResponse(ChallengeListNameResource::collection($getProjectChallengeList), __('responses.found_challenges_list'));
            }
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function labList(Request $request)
    {
        try {
            $checkChallenge = ChallengeService::getChallengeBasedOnUUID($request->challenge_id);
            if ($checkChallenge) {
                $getProjectLabList = $this->projectRepository->getProjectLabs($request, $checkChallenge->id);
                if ($getProjectLabList) {
                    return $this->sendResponse(LabListNameResource::collection($getProjectLabList), __('responses.found_labs_list'));
                }
            } else {
                return $this->sendError(__('responses.not_found_labs_list'), 403);
            }
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getProjectBasedOnSlug($slug)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($checkProjectSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_slug_available'), 200);
            }

            return $this->sendError(__('responses.already_exists'), 400);
        } catch (Exception $e) {
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateProjectRequest $request)
    {
        try {
            $checkChallenge = ChallengeService::getChallengeBasedOnUUID($request->challenge_id);
            if ($checkChallenge) {
                $challengeStatus = ($checkChallenge->status === '1' && $checkChallenge->is_open === '0');
                if ($challengeStatus) {
                    $checkChallengeTimelineType = $checkChallenge->challenge_timelines->timeline_type;
                    if ($checkChallengeTimelineType === '1') {
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectPitchTask(Request $request)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnUUID($request->project_id);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fileUpload(Request $request)
    {
        try {
            $checkProjectExistsOrNot = $this->projectRepository->getProjectBasedOnUUID($request->project_id);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
            }

            $addProjectFiles = $this->projectRepository->projectProjectFile($checkProjectExistsOrNot->id, $request);

            if ($addProjectFiles) {
                return $this->sendResponse(ProjectFileResource::make($checkProjectExistsOrNot), __('responses.project_file_stored_success'), 200);
            }

            return $this->sendError(__('responses.project_file_stored_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $project = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($project) {
                return $this->sendResponse(ProjectResource::make($project), __('responses.found_project_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_project_detail'), 404);
        } catch (\Exception $e) {
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
                $challengeStatus = ($checkChallenge->status === '1' && $checkChallenge->is_open === '0');
                if ($challengeStatus) {
                    $checkChallengeTimelineType = $checkChallenge->challenge_timelines->timeline_type;
                    if ($checkChallengeTimelineType === '1') {
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
            if ($request->cover_media != null) {
                if ($request->media_type == 'image') {
                    $uploaded_cover_media = $this->projectRepository->uploadCoverImage($request->cover_media);
                    if (!$uploaded_cover_media) {
                        return $this->sendError(__('responses.image_upload_failed'), 400);
                    }
                } elseif ($request->media_type == 'embedded') {
                    $uploaded_cover_media = $request->cover_media;
                }

                $update_cover_image = $uploaded_cover_media;
            }

            $updateProject = $this->projectRepository->updateProject($slug, $request, $update_cover_image);
            if ($updateProject != false) {
                return $this->sendResponse(ProjectResource::make($updateProject), __('responses.project_update_successfully'), 200);
            }

            return $this->sendError(__('responses.project_not_update'));
        } catch (Exception $e) {
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

            $getProjectChallengeRequirement = $this->projectRepository->projectRequirements($checkProjectExistsOrNot->id);
            if ($getProjectChallengeRequirement) {
                return $this->sendResponse(ProjectRequirementResource::make($checkProjectExistsOrNot), __('responses.project_requirement_found'), 200);
            }

            return $this->sendError(__('responses.project_not_requirement_found'));
        } catch (Exception $e) {
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
