<?php

namespace App\Http\Controllers\Api\Public\Achievement;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Achievement\AchievementResource;
use App\Http\Resources\Public\ResourceGroup\ResourceGroupResource;
use App\Repositories\Api\Public\Achievement\AchievementRepository;
use Illuminate\Http\Request;

class AchievementController extends AppBaseController
{
    private $achievementRepository;

    public function __construct(AchievementRepository $achievementRepository)
    {
        $this->achievementRepository = $achievementRepository;
    }

    public function index(Request $request)
    {
        try {
            $achievement = $this->achievementRepository->getList($request);
            if ($achievement !== false) {
                $response = [
                    'total_count'  => $achievement->total(),
                    'per_page'     => $achievement->perPage(),
                    'count'        => $achievement->count(),
                    'current_page' => $achievement->currentPage(),
                    'total_pages'  => $achievement->lastPage(),
                    'list'         => AchievementResource::collection($achievement),
                ];

                return $this->sendResponse($response, __('responses.found_achievement_list'));
            }

            return $this->sendError(__('responses.not_found_achievement_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($id){
        try {
            $achievement = $this->achievementRepository->getAchievementBasedOnSlug($id);
            if ($achievement) {
                return $this->sendResponse(AchievementResource::make($achievement), __('responses.found_achievement_list'));
            }
            return $this->sendError(__('responses.not_found_achievement_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
