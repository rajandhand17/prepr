<?php

namespace App\Repositories\Api\TeamMatching;

use App\Models\ProjectMemberManagement;
use App\Services\Manage\ChallengeService;
use App\Services\ProjectMemberManagementService;
use App\Services\ProjectService;
use App\Services\ProjectSkillsService;
use App\Services\UserService;
use App\Services\UserSettingService;

class TeamMatchingRepository implements TeamMatchingInterface
{
    private $projectService;

    private $projectSkillsService;

    private $challengesService;

    private $projectMemberManagementService;

    private $userService;
    public function __construct(UserService $userService,ProjectService $projectService, ProjectSkillsService $projectSkillsService,ChallengeService $challengeService, ProjectMemberManagementService $projectMemberManagementService){
        $this->projectService = $projectService;
        $this->projectSkillsService=$projectSkillsService;
        $this->challengesService=$challengeService;
        $this->projectMemberManagementService=$projectMemberManagementService;
        $this->userService=$userService;
    }

    public function getProjectListingBasedOnSkills($getUsersSkills,$request){
        try {
            $getProjectsIds=$this->projectSkillsService->getProjectsListingBasedOnSkills($getUsersSkills);
            $getChallengeIds=$this->projectService->getProjectListingBasedOnSkills($getProjectsIds,$request);
            return $getChallengeIds;
        }catch (\Exception $e){
            return false;
        }
    }

    public function getBrowsersList($request){
        try {
            $getBrowsersIds=$this->projectService->getBrowsersListing($request);
        }catch (\Exception $e){
            return false;
        }
    }
    public function getPendingRequests($request){
        try {
          $getProjectsPendingList=$this->userService->getUsersBasedOnProjectMemberManagement($request);
            return $getProjectsPendingList;
        }catch (\Exception $e){
            return false;
        }
    }

    public function getMatchingTeams($request){
        try {
            $getProjectsPendingList=$this->projectService->getMatchedTeams($request);
            return $getProjectsPendingList;
        }catch (\Exception $e){
            return false;
        }
    }
}
