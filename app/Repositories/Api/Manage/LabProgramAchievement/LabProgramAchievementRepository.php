<?php

namespace App\Repositories\Api\Manage\LabProgramAchievement;

use App\Helpers\UtilityHelper;
use App\Services\Manage\LabProgramAchievementsService;

class LabProgramAchievementRepository implements LabProgramAchievementInterface
{
    private $labProgramAchievementService;

    public function __construct(LabProgramAchievementsService $labProgramAchievementService)
    {
        $this->labProgramAchievementService = $labProgramAchievementService;
    }

    public function uploadAchievementImage($image)
    {
        try {
            return $this->labProgramAchievementService->uploadAchievementImage($image);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateAchievementImage($image)
    {
        try {
            return $this->labProgramAchievementService->updateAchievementImage($image);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
