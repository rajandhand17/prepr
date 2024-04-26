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
                    $getlist = $this->teamMatchingRepository->getBrowsersList($request);
                    break;
                case 'pending':
                    $getlist = $this->teamMatchingRepository->getPendingRequests($request);
                    break;
                case 'matched':
                    $getlist = $this->teamMatchingRepository->getMatchingTeams($request);

                    break;
            }
            if ($getlist) {
                $response = [
                    'total_count'  => $getlist->total(),
                    'per_page'     => $getlist->perPage(),
                    'count'        => $getlist->count(),
                    'current_page' => $getlist->currentPage(),
                    'total_pages'  => $getlist->lastPage(),
                    'list'         => TeamMatchingResource::collection($getlist),
                ];
            } else {
                $response = [];
            }
            return $this->sendResponse($response, __('responses.team_matching_list_successfully'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
