<?php

namespace App\Http\Controllers\Api\Manage\Project;

use App\Http\Controllers\AppBaseController;
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

    public function create(Request $request)
    {
        try {
            $checkChallenge = ChallengeService::getChallengeBasedOnUUID($request->challenge_id);
            if ($checkChallenge) {
                $challengeStatus = ($checkChallenge->status === '1' && $checkChallenge->is_open === '0');
                if ($challengeStatus) {
                    $checkChallengeTimelineType = $checkChallenge->challenge_timelines->timeline_type;
                    if ($checkChallengeTimelineType === '1') {
                        if(!$checkChallenge->challenge_timelines->application_deadline_date > Carbon::now()->toDateTimeString()){
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
}
