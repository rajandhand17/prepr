<?php

namespace App\Repositories\Api\Dashboard\User;

use App\Helpers\UtilityHelper;
use App\Services\ProjectService;
use App\Services\ProjectSocialActivitiesService;
use App\Services\Public\AchievementService;
use App\Services\Public\ChallengeService;
use App\Services\Public\ChallengeSocialActivitiesService;
use App\Services\Public\LabService;
use App\Services\Public\LabSocialActivitiesService;
use App\Services\Public\MemberManagementService;
use App\Services\Public\ProjectMemberManagementService;
use Exception;

class UserDashboardRepository implements UserDashboardInterface
{
    private $memberManagementService;
    private $challengeSocialActivitiesService;
    private $challengeService;
    private $labSocialActivitiesService;
    private $labService;
    private $projectMemberManagementService;
    private $projectSocialActivitiesService;
    private $projectService;
    private $achievementService;

    public function __construct(MemberManagementService $memberManagementService, ChallengeSocialActivitiesService $challengeSocialActivitiesService, ChallengeService $challengeService, LabSocialActivitiesService $labSocialActivitiesService, LabService $labService, ProjectMemberManagementService $projectMemberManagementService, ProjectSocialActivitiesService $projectSocialActivitiesService, ProjectService $projectService, AchievementService $achievementService)
    {
        $this->memberManagementService = $memberManagementService;
        $this->challengeSocialActivitiesService = $challengeSocialActivitiesService;
        $this->challengeService = $challengeService;
        $this->labSocialActivitiesService = $labSocialActivitiesService;
        $this->labService = $labService;
        $this->projectMemberManagementService = $projectMemberManagementService;
        $this->projectSocialActivitiesService = $projectSocialActivitiesService;
        $this->projectService = $projectService;
        $this->achievementService = $achievementService;
    }

    public function challengeRequestIds($userData, $inviteStatus)
    {
        try {
            return $this->memberManagementService->challengeRequestIds($userData, $inviteStatus);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function challengeFavouriteIds($userData)
    {
        try {
            return $this->challengeSocialActivitiesService->challengeFavouriteIds($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengeList($challengeIds)
    {
        try {
            return $this->challengeService->getChallengeList($challengeIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function labRequestIds($userData, $inviteStatus)
    {
        try {
            return $this->memberManagementService->labRequestIds($userData, $inviteStatus);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function labFavouriteIds($userData)
    {
        try {
            return $this->labSocialActivitiesService->labFavouriteIds($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabList($labIds)
    {
        try {
            return $this->labService->getLabList($labIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function projectRequestIds($userData, $inviteStatus)
    {
        try {
            return $this->projectMemberManagementService->projectRequestIds($userData, $inviteStatus);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function projectFavouriteIds($userData)
    {
        try {
            return $this->projectSocialActivitiesService->projectFavouriteIds($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getDashboardProjectList($projectIds)
    {
        try {
            return $this->projectService->getDashboardProjectList($projectIds);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getMyLatestAchievement($userData)
    {
        try {
            return $this->achievementService->getMyLatestAchievement($userData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
