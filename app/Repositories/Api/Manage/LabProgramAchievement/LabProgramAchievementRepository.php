<?php

namespace App\Repositories\Api\Manage\LabProgramAchievement;

use App\Services\Manage\LabProgramAchievementsService;

class LabProgramAchievementRepository implements LabProgramAchievementInterface
{
    private $LabProgramAchievementService;

    public function __construct(LabProgramAchievementsService $LabProgramAchievementService)
    {
        $this->LabProgramAchievementService = $LabProgramAchievementService;
    }

    public function uploadAchievementImage($image)
    {
        try {
            return $this->LabProgramAchievementService->uploadAchievementImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateAchievementImage($image)
    {
        try {
            return $this->LabProgramAchievementService->updateAchievementImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }
}
