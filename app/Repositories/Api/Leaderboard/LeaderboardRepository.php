<?php

namespace App\Repositories\Api\Leaderboard;


use App\Models\User;
use App\Models\UserPoint;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\UserService;

class LeaderboardRepository implements LeaderboardInterface
{
    private $userService;

    private $labService;

    private $memberManagerService;

        public function __construct(UserService $userService, LabService $labService, MemberManagementService $memberManagerService){
            $this->userService=$userService;
            $this->labService=$labService;
            $this->memberManagerService=$memberManagerService;
        }

        public function getLeaderBoardList($request){
            try {
                $user=$this->userService->getLeaderBoardList($request);
                return $user;
            }catch (\Exception $e){
                return false;
            }
        }

        public function getComponentsMembers($slug,$component){
            try {
                switch ($component){
                    case 'lab':
                    $component=$this->memberManagerService->getMembersBasedOnComponentId();
                    break;
                    default:
                        $component=$this->memberManagerService->getMembersBasedOnComponentId();
                }
                return $this->userService->getComponentBasedUsers($slug,$component);

            }catch (\Exception $e){
                return false;
            }
        }
 }
