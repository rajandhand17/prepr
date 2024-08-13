<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabAcheivement;
use App\Models\LabMarketplaceAchievement;
use Exception;

class LabMarketplaceAchievementsService
{
    public static function addLabMarketplaceAchievements($labMarketplaceId, $labId)
    {
        try {
            $existingLabAchievements = LabAcheivement::where('lab_id', $labId)->first();
            if ($existingLabAchievements) {
                $labAchievement = new LabMarketplaceAchievement();
                $labAchievement->lab_marketplace_id = $labMarketplaceId;
                $labAchievement->achievement_name = $existingLabAchievements->achievement_name;
                $labAchievement->achievement_points = $existingLabAchievements->achievement_points;
                $labAchievement->achievement_condition = $existingLabAchievements->achievement_condition;
                $labAchievement->achievement_image = $existingLabAchievements->getRawOriginal('achievement_image');
                $labAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
