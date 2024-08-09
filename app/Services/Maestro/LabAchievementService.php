<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;

class LabAchievementService
{
    public static function createCloneLabAchievement($originalLabsAchievement, $clonedLabId)
    {
        try {
            if ($originalLabsAchievement) {
                $cloneLabAchievement = $originalLabsAchievement->replicate();
                $cloneLabAchievement->lab_id = $clonedLabId;
                $cloneLabAchievement->save();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
