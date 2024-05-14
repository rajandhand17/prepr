<?php

namespace App\Http\Controllers\Api\Leaderboard;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Leaderboard\LeaderboardResource;
use App\Repositories\Api\Leaderboard\LeaderboardRepository;
use Illuminate\Http\Request;

class LeaderboardController extends AppBaseController
{
    private $leaderboardRepository;

    public function __construct(LeaderboardRepository $leaderboardRepository)
    {
        $this->leaderboardRepository = $leaderboardRepository;
    }

    public function index(Request $request)
    {
        try {
            $user = $this->leaderboardRepository->getLeaderBoardList($request);
            if ($user) {
                return $this->sendResponse(LeaderboardResource::collection($user), __('responses.leaderboard_list'));
            }

            return $this->sendResponse([], __('responses.leaderboard_list'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function ComponentBasedLeaderboard($slug, $component, Request $request)
    {
        try {
            $components = [
                'lab',
            ];
            if (!in_array($component, $components)) {
                return $this->sendError(__('responses.valid_component_error'));
            }
            $getUsersListing = $this->leaderboardRepository->getComponentsMembers($slug, $component, $request);
            if ($getUsersListing) {
                return $this->sendResponse(LeaderboardResource::collection($getUsersListing), __('responses.get_users_listing_successfully'));
            }

            return $this->sendResponse([], __('responses.get_users_listing_successfully'));
        } catch (\Exception $e) {
            return false;
        }
    }
}
