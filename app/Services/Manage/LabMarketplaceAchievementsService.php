<?php

namespace App\Services\Manage;

use App\Models\LabAcheivement;
use App\Models\LabMarketplaceAchievement;

class LabMarketplaceAchievementsService
{
    public function createLabMarketplaceAchievements($labMarketplaceId, $labId)
    {
        try {
            $existingLabAchievements = LabAcheivement::where('lab_id', $labId)->first();
            if ($existingLabAchievements) {
                $labAchievement = new LabMarketplaceAchievement();
                $labAchievement->lab_marketplace_id = $labMarketplaceId;
                $labAchievement->achievement_name = $existingLabAchievements->achievement_name;
                $labAchievement->achievement_points = $existingLabAchievements->achievement_points;
                $labAchievement->achievement_condition = json_encode($existingLabAchievements->achievement_condition);
                $labAchievement->achievement_image = $existingLabAchievements->achievement_image;
                $labAchievement->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteLabMarketplaceAchievement($labMarketplaceId)
    {
        try {
            $checkLabMarketplaceAchievement = LabMarketplaceAchievement::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($checkLabMarketplaceAchievement) {
                $deleteLabMarketplaceAchievement = LabMarketplaceAchievement::where('lab_marketplace_id', $checkLabMarketplaceAchievement)->delete();
                if (!$deleteLabMarketplaceAchievement) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
