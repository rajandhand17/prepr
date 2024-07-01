<?php

namespace App\Repositories\Api\Manage\LabAchievement;

use App\Helpers\UtilityHelper;
use App\Services\Manage\LabAcheivementService;

class LabAchievementRepository implements LabAchievementInterface
{
    private $LabAcheivementService;
    private $memberManagementService;

    public function __construct(LabAcheivementService $LabAcheivementService)
    {
        $this->LabAcheivementService = $LabAcheivementService;
    }

    public function uploadAcheivementImage($image)
    {
        try {
            return $this->LabAcheivementService->uploadAcheivementImage($image);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateAcheivementImage($image)
    {
        try {
            return $this->LabAcheivementService->updateAcheivementImage($image);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
