<?php

namespace App\Repositories\Api\Public\Achievement;

use App\Helpers\UtilityHelper;
use App\Services\Public\AchievementService;
use Exception;

class AchievementRepository implements AchievementInterface
{
    private $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function getList($request)
    {
        try {
            return $this->achievementService->getList($request);
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getAchievementBasedOnCertificateNumber($certificate_id)
    {
        try {
            return $this->achievementService->getAchievementBasedOnCertificateNumber($certificate_id);
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function downloadCertificate($certificate_id, $format)
    {
        try {
            return $this->achievementService->downloadCertificate($certificate_id, $format);
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getAchievementList($userId, $request)
    {
        try {
            return $this->achievementService->getAchievementList($userId, $request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getColumnValue($request)
    {
        try {
            return $this->achievementService->getColumnValue($request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkachievementActivity($certificate_id, $action)
    {
        try {
            return $this->achievementService->checkachievementActivity($certificate_id, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function achievementActivity($certificate_id, $action)
    {
        try {
            return $this->achievementService->achievementActivity($certificate_id, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
