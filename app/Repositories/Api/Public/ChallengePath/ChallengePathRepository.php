<?php

namespace App\Repositories\Api\Public\ChallengePath;

use App\Helpers\UtilityHelper;
use App\Services\Public\ChallengePathService;
use App\Services\Public\ChallengePathSocialActivitiesService;
use Exception;

class ChallengePathRepository implements ChallengePathInterface
{
    private $challengePathService;
    private $challengePathSocialActivitiesService;

    public function __construct(ChallengePathService $challengePathService, ChallengePathSocialActivitiesService $challengePathSocialActivitiesService)
    {
        $this->challengePathService = $challengePathService;
        $this->challengePathSocialActivitiesService = $challengePathSocialActivitiesService;
    }

    public function getList($request)
    {
        try {
            return $this->challengePathService->getList($request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getChallengePathBasedOnSlug($slug)
    {
        try {
            return $this->challengePathService->getChallengePathBasedOnSlug($slug);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->challengePathSocialActivitiesService->getColumnNameValue($action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkSocialActivity($challengePath, $column, $action)
    {
        try {
            return $this->challengePathSocialActivitiesService->checkSocialActivity($challengePath, $column, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function captureSocialActivity($challengePath, $column, $value)
    {
        try {
            return $this->challengePathSocialActivitiesService->captureSocialActivity($challengePath, $column, $value);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
