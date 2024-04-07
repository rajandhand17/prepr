<?php

namespace App\Http\Controllers\Api\TeamMatching;

use App\Http\Controllers\AppBaseController;
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

    public function pendingRequests($action, Request $request)
    {
        try {
            if (!in_array($action, ['browse', 'pending', 'matched'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($action) {
                case 'browse':
                    $getBrowserRequest = $this->teamMatchingRepository->getBrowsersList($request);
                    if ($getBrowserRequest) {
                        $response = [
                            'total_count'  => $getBrowserRequest->total(),
                            'per_page'     => $getBrowserRequest->perPage(),
                            'count'        => $getBrowserRequest->count(),
                            'current_page' => $getBrowserRequest->currentPage(),
                            'total_pages'  => $getBrowserRequest->lastPage(),
                            'list'         => TeamMatchingResource::collection($getBrowserRequest),
                        ];
                        $message = __('responses.team_matching_list_successfully');
                    } else {
                        $response = [];
                        $message = __('responses.team_matching_list_successfully');
                    }
                    break;
                case 'pending':
                    $getPendingRequests = $this->teamMatchingRepository->getPendingRequests($request);
                    if (!empty($getPendingRequests)) {
                        $response = [
                            'total_count'  => $getPendingRequests->total(),
                            'per_page'     => $getPendingRequests->perPage(),
                            'count'        => $getPendingRequests->count(),
                            'current_page' => $getPendingRequests->currentPage(),
                            'total_pages'  => $getPendingRequests->lastPage(),
                            'list'         => TeamMatchingResource::collection($getPendingRequests),
                        ];
                        $message = __('responses.team_matching_list_successfully');
                    } else {
                        $response = [];
                        $message = __('responses.team_matching_list_successfully');
                    }
                    break;
                case 'matched':
                    $getMatchingRequest = $this->teamMatchingRepository->getMatchingTeams($request);
                    if (!empty($getMatchingRequest)) {
                        $response = [
                            'total_count'  => $getMatchingRequest->total(),
                            'per_page'     => $getMatchingRequest->perPage(),
                            'count'        => $getMatchingRequest->count(),
                            'current_page' => $getMatchingRequest->currentPage(),
                            'total_pages'  => $getMatchingRequest->lastPage(),
                            'list'         => TeamMatchingResource::collection($getMatchingRequest),
                        ];
                        $message = __('responses.team_matching_list_successfully');
                    } else {
                        $response = [];
                        $message = __('responses.team_matching_list_successfully');
                    }
                    break;
            }

            return $this->sendResponse($response, $message);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
