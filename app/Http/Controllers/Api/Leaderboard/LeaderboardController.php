<?php

namespace App\Http\Controllers\Api\Leaderboard;

use App\Helpers\UtilityHelper;
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
            if ($user->count() > 0) {
                $response = [
                    'total_count'  => $user->total(),
                    'per_page'     => $user->perPage(),
                    'count'        => $user->count(),
                    'current_page' => $user->currentPage(),
                    'total_pages'  => $user->lastPage(),
                    'list'         => LeaderboardResource::collection($user),
                ];

                return $this->sendResponse($response, __('responses.leaderboard_list'));
            }

            return $this->sendResponse([], __('responses.leaderboard_list'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError('responses.send_error', 500);
        }
    }

    public function ComponentBasedLeaderboard($slug, $component, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 404);
            }
            $getUsersListing = $this->leaderboardRepository->getComponentsMembers($checkComponentBasedOnSlug->id, $component, $request);
            if ($getUsersListing->count() > 0) {
                $response = [
                    'total_count'  => $getUsersListing->total(),
                    'per_page'     => $getUsersListing->perPage(),
                    'count'        => $getUsersListing->count(),
                    'current_page' => $getUsersListing->currentPage(),
                    'total_pages'  => $getUsersListing->lastPage(),
                    'list'         => LeaderboardResource::collection($getUsersListing),
                ];

                return $this->sendResponse($response, __('responses.leaderboard_list'));
            }

            return $this->sendResponse([], __('responses.get_users_listing_successfully'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError('responses.send_error', 500);
        }
    }
}
