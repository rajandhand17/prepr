<?php

namespace App\Http\Controllers\Api\Public\Achievement;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Achievement\AchievementResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Repositories\Api\Public\Achievement\AchievementRepository;
use Illuminate\Http\Request;

class AchievementController extends AppBaseController
{
    private $achievementRepository;

    public function __construct(AchievementRepository $achievementRepository){
        $this->achievementRepository = $achievementRepository;
    }

    public function index(Request $request){
        try {
            $achievement=$this->achievementRepository->getList($request);
            if ($achievement !== false) {
                $response = [
                    'total_count'  => $achievement->total(),
                    'per_page'     => $achievement->perPage(),
                    'count'        => $achievement->count(),
                    'current_page' => $achievement->currentPage(),
                    'total_pages'  => $achievement->lastPage(),
                    'list'         => AchievementResource::collection($achievement),
                ];

                return $this->sendResponse($response, __('responses.found_challenges_list'));
            }
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
