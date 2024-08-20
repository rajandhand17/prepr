<?php

namespace App\Http\Controllers\Api\TeamMatching;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\TeamMatching\PendingRequestsResources;
use App\Http\Resources\TeamMatching\TeamMatchingResource;
use App\Http\Resources\User\UserResource;
use App\Repositories\Api\TeamMatching\TeamMatchingRepository;
use Illuminate\Http\Request;

class TeamMatchingController extends AppBaseController
{
    private $teamMatchingRepository;

    public function __construct(TeamMatchingRepository $teamMatchingRepository)
    {
        $this->teamMatchingRepository = $teamMatchingRepository;
    }

    public function browseMatchedPendingRequests($action, Request $request)
    {
        try {
            // Checking actions are between browser,matched or pending otherwise will raise error
            if (!in_array($action, ['browse', 'matched', 'pending'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            // Current user details
            $userData = auth()->user();
            $response = [];
            switch ($action) {
                case 'browse':
                    // Getting all project's ids.
                    $getProjectIds = $this->teamMatchingRepository->getBrowsersList($userData);
                    break;
                case 'pending':
                    // Getting all project's ids in which users requested to join
                    $getProjectIds = $this->teamMatchingRepository->getPendingRequests($userData);
                    break;
                case 'matched':
                    // Getting project's ids in which users are invited
                    $getProjectIds = $this->teamMatchingRepository->getMatchingTeams();
                    break;
            }
            if ($getProjectIds) {
                if ($action == 'pending') {
                    // Fetching project's ids without pagination
                    $projectIds = $this->teamMatchingRepository->getProjectListWithoutPagination($getProjectIds, $request);
                    // Getting user's details based on project ids
                    $getDetails = $this->teamMatchingRepository->getUsersBasedOnProjectIds($projectIds);
                    // Setup resources based on action
                    $resource = PendingRequestsResources::collection($getDetails);
                } else {
                    // Fetching project's ids with pagination
                    $getDetails = $this->teamMatchingRepository->getProjectList($getProjectIds, $request);
                    // Setup resources based on action
                    $resource = TeamMatchingResource::collection($getDetails);
                }
                if ($getDetails !== false) {
                    $response = [
                        'total_count'  => $getDetails->total(),
                        'per_page'     => $getDetails->perPage(),
                        'count'        => $getDetails->count(),
                        'current_page' => $getDetails->currentPage(),
                        'total_pages'  => $getDetails->lastPage(),
                        'list'         => $resource,
                    ];
                }
            }

            return $this->sendResponse($response, __('responses.team_matching_list_successfully'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function sendRequest($slug)
    {
        try {
            $checkSlugExistsOrNot = $this->teamMatchingRepository->checkSlug($slug);
            if (!$checkSlugExistsOrNot) {
                return $this->sendError(__('responses.slug_not_found'), 404);
            }
            $checkRequestExistsOrNot = $this->teamMatchingRepository->checkRequestExistsOrNotExists($checkSlugExistsOrNot->id);
            if (isset($checkRequestExistsOrNot->invite_status) && $checkRequestExistsOrNot->invite_status != '3') {
                return $this->sendError(__('responses.already_request_exists'), 402);
            }
            $sendRequest = $this->teamMatchingRepository->sendRequest($checkSlugExistsOrNot->id);
            if ($sendRequest) {
                return $this->sendResponse([], __('responses.send_request_successfully'));
            }

            return $this->sendError(__('responses.send_request_failed'), 403);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getTeamMatchingProfileCheck()
    {
        try {
            $checkComponentTeamMatching = $this->teamMatchingRepository->checkComponentTeamMatching();
            if ($checkComponentTeamMatching) {
                return $this->sendError(__('responses.already_completed_team_matching'), 400);
            }
            $this->teamMatchingRepository->completeTeamMatching();

            return $this->sendResponse(UserResource::make(auth()->user()), __('responses.team_matching_updated_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getTeamMatchingCount()
    {
        try {
            $response = $this->teamMatchingRepository->getCountForTeamMatching();

            return $this->sendResponse($response, __('responses.team_matching_count_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
