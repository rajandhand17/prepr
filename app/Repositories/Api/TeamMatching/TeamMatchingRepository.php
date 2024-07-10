<?php

namespace App\Repositories\Api\TeamMatching;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use App\Services\ProjectMemberManagementService;
use App\Services\ProjectService;
use App\Services\ProjectSkillsService;
use App\Services\UserService;

class TeamMatchingRepository implements TeamMatchingInterface
{
    private $projectService;

    private $projectSkillsService;

    private $challengesService;

    private $projectMemberManagementService;

    private $userService;

    public function __construct(UserService $userService, ProjectService $projectService, ProjectSkillsService $projectSkillsService, ChallengeService $challengeService, ProjectMemberManagementService $projectMemberManagementService)
    {
        $this->projectService = $projectService;
        $this->projectSkillsService = $projectSkillsService;
        $this->challengesService = $challengeService;
        $this->projectMemberManagementService = $projectMemberManagementService;
        $this->userService = $userService;
    }

    public function getBrowsersList($request)
    {
        try {
            $getBrowsersIds = $this->projectService->getBrowsersListing($request);

            return $getBrowsersIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getPendingRequests($userData)
    {
        try {
            $getProjectsIds = $this->projectMemberManagementService->getPendingRequests($userData);
             return $getProjectsIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getMatchingTeams()
    {
        try {
            $getProjectsPendingList = $this->projectMemberManagementService->getMatchedTeams();

            return $getProjectsPendingList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getProjectList($getProjectIds, $request)
    {
        try {
            return $this->projectService->getProjectList($getProjectIds, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
    public function getProjectListWithoutPagination($getProjectIds, $request)
    {
        try {
            return $this->projectService->getProjectListWithoutPagination($getProjectIds, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getUsersBasedOnProjectIds($userData,$projectids)
    {
        try {
            return $this->projectMemberManagementService->getPendingRequests($userData,$projectids);
        }catch (\Exception $e){
            return false;
        }
    }
    public function checkSlug($slug)
    {
        try {
            return $this->projectService->getProjectBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function sendRequest($projectId)
    {
        try {
            return $this->projectMemberManagementService->sendRequest($projectId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkRequestExistsOrNotExists($projectId)
    {
        try {
            return $this->projectMemberManagementService->checkRequestExistsOrNotExists($projectId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
