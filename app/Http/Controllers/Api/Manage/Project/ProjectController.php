<?php

namespace App\Http\Controllers\Api\Manage\Project;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Project\CreateProjectRequest;
use App\Http\Resources\Manage\Challenge\ChallengeListNameResource;
use App\Http\Resources\Manage\Lab\LabListNameResource;
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
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnUUID($request->project_id);
            if ($checkProjectSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
            }

            $checkChallenge = ChallengeService::getChallengeBasedOnId($checkProjectSlugExistsOrNot->challenge_id);
            if ($checkChallenge == false) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }
            $addPitchTask = $this->projectRepository->projectPitchTask($checkProjectSlugExistsOrNot->id, $request);

            if ($addPitchTask) {
                return $this->sendResponse(ProjectResource::make($checkProjectSlugExistsOrNot), __('responses.project_pitch_stored_success'), 200);
            }

            return $this->sendError(__('responses.project_pitch_stored_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function fileUpload(Request $request)
    {
        try {
            $checkProjectSlugExistsOrNot = $this->projectRepository->getProjectBasedOnUUID($request->project_id);
            if ($checkProjectSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
            }

            $addProjectFiles = $this->projectRepository->projectProjectFile($checkProjectSlugExistsOrNot->id, $request);

            if ($addProjectFiles) {
                return $this->sendResponse(ProjectResource::make($checkProjectSlugExistsOrNot), __('responses.project_file_stored_success'), 200);
            }

            return $this->sendError(__('responses.project_file_stored_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
