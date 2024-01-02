<?php

namespace App\Services\Manage;

use App\Models\LabAcheivement;
use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTemplateExternalLink;
use App\Models\LabTemplateSkillsGroupsStack;
use App\Models\TemplateLabAchievement;

class LabMarketplaceAchievementsService
{
    public function createLabMarketplaceAchievements($labMarketplaceId,$lab){
        try {
            $existingLabAchievements = LabAcheivement::where('lab_id', $lab->id)->first();
            if ($existingLabAchievements) {
                $labAchievement = new TemplateLabAchievement();
                $labAchievement->template_lab_id = $labMarketplaceId->id;
                $labAchievement->achievement_name = $existingLabAchievements->achievement_name;
                $labAchievement->achievement_points = $existingLabAchievements->achievement_points;
                $labAchievement->achievement_condition =json_encode($existingLabAchievements->achievement_condition);
                $labAchievement->achievement_image = $existingLabAchievements->achievement_image;
                $labAchievement->save();
            }
            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
