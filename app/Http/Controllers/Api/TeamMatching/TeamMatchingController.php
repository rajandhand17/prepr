<?php

namespace App\Http\Controllers\Api\TeamMatching;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\TeamMatching\PendingRequestsResources;
use App\Http\Resources\TeamMatching\TeamMatchingResource;
use App\Repositories\Api\TeamMatching\TeamMatchingRepository;
use Illuminate\Http\Request;

class TeamMatchingController extends AppBaseController
{
    private $teamMatchingRepository;

    public function __construct(TeamMatchingRepository $teamMatchingRepository)
    {
        $this->teamMatchingRepository = $teamMatchingRepository;
    }
    public function getPendingRequests(Request $request)
    {
        try {
            //getting current logged users details
            $userData = auth()->user();
            //getting those projects ids in which users have send requests
            $getProjectIds = $this->teamMatchingRepository->getPendingRequests($userData);
            if($getProjectIds){
                //filtering all projects based on fronted requests
                $projectids = $this->teamMatchingRepository->getProjectListWithoutPagination($getProjectIds, $request);
                $getPendingRequests=$this->teamMatchingRepository->getUsersBasedOnProjectIds($userData,$projectids);
                if ($getPendingRequests !== false) {
                    $response = [
                        'total_count'  => $getPendingRequests->total(),
                        'per_page'     => $getPendingRequests->perPage(),
                        'count'        => $getPendingRequests->count(),
                        'current_page' => $getPendingRequests->currentPage(),
                        'total_pages'  => $getPendingRequests->lastPage(),
                        'list'         => PendingRequestsResources::collection($getPendingRequests),
                    ];
                }
                return $this->sendResponse($response,__("responses.team_matching_list_successfully"));
            }
        }catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function browseMatchedPendingRequests($action, Request $request)
    {
        try {
            if (!in_array($action, ['browse', 'pending', 'matched'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $userData = auth()->user();
            $response = [];
            switch ($action) {
                case 'browse':
                    $getProjectIds = $this->teamMatchingRepository->getBrowsersList($userData);
                    break;
                case 'pending':
                    $getProjectIds = $this->teamMatchingRepository->getPendingRequests($userData);
                    break;
                case 'matched':
                    $getProjectIds = $this->teamMatchingRepository->getMatchingTeams();
                    break;
            }
            if ($getProjectIds) {
                $project = $this->teamMatchingRepository->getProjectList($getProjectIds, $request);
                if ($project !== false) {
                    $response = [
                        'total_count'  => $project->total(),
                        'per_page'     => $project->perPage(),
                        'count'        => $project->count(),
                        'current_page' => $project->currentPage(),
                        'total_pages'  => $project->lastPage(),
                        'list'         => TeamMatchingResource::collection($project),
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
}
