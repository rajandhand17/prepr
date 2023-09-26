<?php

namespace App\Repositories\Api\Public\Challenge;

use App\Services\Public\ChallengeService;
use App\Services\Public\ChallengeSocialActivitiesService;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $challengeSocialActivitiesService;

    public function __construct(ChallengeService $challengeService, ChallengeSocialActivitiesService $challengeSocialActivitiesService)
    {
        $this->challengeService = $challengeService;
        $this->challengeSocialActivitiesService = $challengeSocialActivitiesService;
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
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->challengeSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($challenge_id, $column, $action)
    {
        try {
            return $this->challengeSocialActivitiesService->checkSocialActivity($challenge_id, $column, $action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($challenge_id, $column, $value)
    {
        try {
            return $this->challengeSocialActivitiesService->captureSocialActivity($challenge_id, $column, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

}
