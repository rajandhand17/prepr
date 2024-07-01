<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\LabProgramsAchievement;

class LabProgramAchievementsService
{
    public function uploadAchievementImage($image)
    {
        try {
            $upload_Achievement_image = FileUploadHelper::uploadImageToS3($image, 'achievement');
            if ($upload_Achievement_image == false) {
                return false;
            }

            return $upload_Achievement_image;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateAchievementImage($image)
    {
        try {
            $upload_Achievement_image = FileUploadHelper::uploadbase64ImageToS3($image, 'achievement');
            if ($upload_Achievement_image == false) {
                return false;
            }

            return $upload_Achievement_image;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createLabProgramAchievement($request, $labProgramId, $upload_achievements_image)
    {
        $achievementCondition = json_encode($request->achievement_condition);
        $labProgramAchievement = new LabProgramsAchievement();
        $labProgramAchievement->lab_program_id = $labProgramId;
        $labProgramAchievement->achievement_name = $request->achievement_name;
        $labProgramAchievement->achievement_points = $request->achievement_points;
        $labProgramAchievement->achievement_image = $upload_achievements_image;
        $labProgramAchievement->save();

        return true;
    }

    public function updateLabProgramAchievement($request, $lab_programs_id, $upload_achievement_image)
    {
        try {
            $checkExistsLabAchievement = LabProgramsAchievement::where('lab_program_id', $lab_programs_id)->first();
            if (!$checkExistsLabAchievement) {
                $labAchievement = new LabProgramsAchievement();
                $labAchievement->lab_program_id = $lab_programs_id;
                $labAchievement->achievement_name = $request->achievement_name;
                $labAchievement->achievement_points = $request->achievement_points;
                $labAchievement->achievement_image = $upload_achievement_image;
                $labAchievement->save();

                return true;
            }
            $checkExistsLabAchievement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $checkExistsLabAchievement->achievement_name;
            $checkExistsLabAchievement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $checkExistsLabAchievement->achievement_points;
            $checkExistsLabAchievement->achievement_image = ($upload_achievement_image) ? $upload_achievement_image : $checkExistsLabAchievement->achievement_image;
            $checkExistsLabAchievement->save();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLabAchievement($lab_id)
    {
        try {
            $checkLabAchievementExists = LabProgramsAchievement::where('lab_programs_id', $lab_id)->first();
            if ($checkLabAchievementExists) {
                $deleteLabAchievement = LabProgramsAchievement::where('lab_programs_id', $lab_id)->delete();
                if (!$deleteLabAchievement) {
                    return false;
                }

                return true;
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
