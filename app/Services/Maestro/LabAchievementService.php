<?php

namespace App\Services\Maestro;

use App\Models\LabAcheivement;

class LabAchievementService
{
    public static function createLabAchievement($originalLabsAchievement, $clonedLabId)
    {
        try {
            if ($originalLabsAchievement) {
                $cloneLabAchievement = $originalLabsAchievement->replicate();
                $cloneLabAchievement->lab_id = $clonedLabId;
                $cloneLabAchievement->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
