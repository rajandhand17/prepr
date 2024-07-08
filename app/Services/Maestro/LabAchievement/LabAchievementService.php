<?php

namespace App\Services\Maestro\LabAchievement;

use App\Helpers\FileUploadHelper;
use App\Models\LabAcheivement;

class LabAchievementService
{
    public static function createLabAchievement($lab, $newLab)
    {
        try {
            $labAchievementData=LabAcheivement::where('lab_id',$lab->id)->first();
            if($labAchievementData){
                $labAchievement = new LabAcheivement();
                $labAchievement->lab_id = $newLab->id;
                $labAchievement->achievement_name = $labAchievementData->achievement_name;
                $labAchievement->achievement_points = $labAchievementData->achievement_points;
                $labAchievement->achievement_condition = $labAchievementData->achievement_conditions;
                $labAchievement->achievement_image = $labAchievementData->achievement_image;
                $labAchievement->save();
            }
            return true;
        }catch (\Exception $e) {
            return false;
        }
    }
}
