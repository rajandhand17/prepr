<?php

namespace App\Repositories\Api\Dashboard\User;

use App\Helpers\UtilityHelper;
use App\Services\Public\ChallengeService;
use App\Services\Public\ChallengeSocialActivitiesService;
use App\Services\Public\MemberManagementService;
use Exception;

class UserDashboardRepository implements UserDashboardInterface
{

    private $memberManagementService;
    private $challengeSocialActivitiesService;
    private $challengeService;

    public function __construct(MemberManagementService $memberManagementService, ChallengeSocialActivitiesService $challengeSocialActivitiesService, ChallengeService $challengeService)
    {
        $this->memberManagementService = $memberManagementService;
        $this->challengeSocialActivitiesService = $challengeSocialActivitiesService;
        $this->challengeService = $challengeService;
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
}
