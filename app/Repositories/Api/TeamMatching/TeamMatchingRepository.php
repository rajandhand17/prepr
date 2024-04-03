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

    public function getProjectListingBasedOnSkills($getUsersSkills){
        try {
            $getProjectsIds=$this->projectSkillsService->getProjectsListingBasedOnSkills($getUsersSkills);
            $getChallengeIds=$this->projectService->getProjectListingBasedOnSkills($getProjectsIds);
            return $getChallengeIds;
          //  $getChallengesDueOrNot=ChallengeService::fetchChallengeDueDate();
            //return $getProjectsList;
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

    public function getMatchingTeams(){
        try {
            $getProjectsPendingList=$this->projectService->getMatchedTeams();
            return $getProjectsPendingList;
        }catch (\Exception $e){
            return false;
        }
    }
}
