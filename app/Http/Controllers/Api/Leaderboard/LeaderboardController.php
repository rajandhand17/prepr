<?php

namespace App\Http\Controllers\Api\Leaderboard;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Leaderboard\LeaderboardResource;
use App\Models\User;
use App\Repositories\Api\Leaderboard\LeaderboardRepository;
use Illuminate\Http\Request;

class LeaderboardController extends AppBaseController
{
    private $leaderboardRepository;
    public function __construct(LeaderboardRepository $leaderboardRepository){
        $this->leaderboardRepository=$leaderboardRepository;
    }

    public function index(){
        try {
            $user=$this->leaderboardRepository->getLeaderBoardList();
            if ($user){
                return $this->sendResponse(LeaderboardResource::collection($user),__('responses.leaderboard_list'));
            }
        }catch (\Exception $e) {
            return false;
        }
    }
}
