<?php

namespace App\Repositories\Api\Public\Challenge;

use App\Services\Public\ChallengeService;
use App\Services\Public\LabSocialActivitiesService;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $labSocialActivitiesService;
    private $memberManagementService;

    public function __construct(ChallengeService $challengeService, LabSocialActivitiesService $labSocialActivitiesService, MemberManagementService $memberManagementService)
    {
        $this->challengeService = $challengeService;
        $this->labSocialActivitiesService = $labSocialActivitiesService;
        $this->memberManagementService = $memberManagementService;
    }

    public function getList($request)
    {
        try {
            return $this->challengeService->getList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return $this->challengeService->getLabBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->labSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($challenge_id, $column, $action)
    {
        try {
            return $this->labSocialActivitiesService->checkSocialActivity($challenge_id, $column, $action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($challenge_id, $column, $value)
    {
        try {
            return $this->labSocialActivitiesService->captureSocialActivity($challenge_id, $column, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

}
