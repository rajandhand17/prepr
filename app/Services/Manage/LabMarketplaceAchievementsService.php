<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabAcheivement;
use App\Models\LabMarketplaceAchievement;
use Exception;

class LabMarketplaceAchievementsService
{
    public function addLabMarketplaceAchievements($labMarketplaceId, $labId)
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

    public function redeemLabMarketplaceAchievement($redeemLabId, $labMarketplaceId)
    {
        try {
            $labMarketplaceAchievementData = LabMarketplaceAchievement::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($labMarketplaceAchievementData) {
                $newLabAchievement = new LabAcheivement();
                $newLabAchievement->lab_id = $redeemLabId;
                $newLabAchievement->achievement_name = $labMarketplaceAchievementData->achievement_name;
                $newLabAchievement->achievement_points = $labMarketplaceAchievementData->achievement_points;
                $newLabAchievement->achievement_condition = $labMarketplaceAchievementData->achievement_condition;
                $newLabAchievement->achievement_image = $labMarketplaceAchievementData->getRawOriginal('achievement_image');
                $newLabAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
