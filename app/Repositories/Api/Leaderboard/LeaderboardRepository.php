<?php

namespace App\Repositories\Api\Leaderboard;


use App\Models\User;
use App\Models\UserPoint;
use App\Services\UserService;

class LeaderboardRepository implements LeaderboardInterface
{
    private $userService
        public function __construct(UserService $userService){
            $this->userService=$userService;
        }

        public function index(){
            try {
                $user=$this->userService->index();
                return $user;
            }catch (\Exception $e){
                return false;
            }
        }
}
