<?php

namespace App\Repositories\Api\Leaderboard;


use App\Models\User;
use App\Models\UserPoint;
use App\Services\UserService;

class LeaderboardRepository implements LeaderboardInterface
{
    private $userService;
        public function __construct(UserService $userService){
            $this->userService=$userService;
        }

        public function getLeaderBoardList($request){
            try {
                $user=$this->userService->getLeaderBoardList($request);
                return $user;
            }catch (\Exception $e){
                return false;
            }
        }
}
