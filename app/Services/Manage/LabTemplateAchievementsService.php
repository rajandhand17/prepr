<?php

namespace App\Services\Manage;

use App\Models\LabAcheivement;
use App\Models\LabTemplateAchievement;

class LabTemplateAchievementsService
{
    public function createLabTemplateAchievement($createLab, $lab)
    {
        try {
            $existingLabAchievements = LabAcheivement::where('lab_id', $lab->id)->first();
            if ($existingLabAchievements) {
                $labAchievement = new LabTemplateAchievement();
                $labAchievement->template_lab_id = $createLab->id;
                $labAchievement->achievement_name = $existingLabAchievements->achievement_name;
                $labAchievement->achievement_points = $existingLabAchievements->achievement_points;
                $labAchievement->achievement_condition = $existingLabAchievements->achievement_conditions;
                $labAchievement->achievement_image = $existingLabAchievements->achievement_image;
                $labAchievement->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
