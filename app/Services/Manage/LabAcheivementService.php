<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\LabAcheivement;

class LabAcheivementService
{
    public function uploadAcheivementImage($image)
    {
        try {
            $upload_acheivement_image = FileUploadHelper::uploadImageToS3($image, 'achievement');
            if ($upload_acheivement_image == false) {
                return false;
            }

            return $upload_acheivement_image;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateAcheivementImage($image)
    {
        try {
            $upload_acheivement_image = FileUploadHelper::uploadbase64ImageToS3($image, 'achievement');
            if ($upload_acheivement_image == false) {
                return false;
            }

            return $upload_acheivement_image;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createLabAchievement($request, $lab, $upload_achievements_image)
    {
        $labAchievement = new LabAcheivement();
        $labAchievement->lab_id = $lab->id;
        $labAchievement->achievement_name = $request->achievement_name;
        $labAchievement->achievement_points = $request->achievement_points;
        $labAchievement->achievement_condition = $request->achievement_conditions;
        $labAchievement->achievement_image = $upload_achievements_image;
        $labAchievement->save();

        return true;
    }

    public function updateLabAchievement($request, $lab_id, $upload_achievement_image)
    {
        try {
            $checkExistsLabAcheivement = LabAcheivement::where('lab_id', $lab_id)->first();
            if (!$checkExistsLabAcheivement) {
                $labAchievement = new LabAcheivement();
                $labAchievement->lab_id = $lab_id;
                $labAchievement->achievement_name = $request->achievement_name;
                $labAchievement->achievement_points = $request->achievement_points;
                $labAchievement->achievement_condition = $request->achievement_conditions;
                $labAchievement->achievement_image = $upload_achievement_image;
                $labAchievement->save();

                return true;
            }
            $checkExistsLabAcheivement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $checkExistsLabAcheivement->achievement_name;
            $checkExistsLabAcheivement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $checkExistsLabAcheivement->achievement_points;
            $checkExistsLabAcheivement->achievement_condition = ($request->has('achievement_conditions')) ? $request->achievement_conditions : $checkExistsLabAcheivement->achievement_conditions;
            $checkExistsLabAcheivement->achievement_image = ($upload_achievement_image) ? $upload_achievement_image : $checkExistsLabAcheivement->achievement_image;
            $checkExistsLabAcheivement->save();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLabAchievement($lab_id)
    {
        try {
            $checkLabAchievementExists = LabAcheivement::where('lab_id', $lab_id)->first();
            if ($checkLabAchievementExists) {
                $deleteLabAchievement = LabAcheivement::where('lab_id', $lab_id)->delete();
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

    public static function getLabAchivements($lab_id)
    {
        try {
            $getLabAchivements = LabAcheivement::where('lab_id', $lab_id)->first();

            return $getLabAchivements;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
