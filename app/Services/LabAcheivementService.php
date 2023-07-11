<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\LabAcheivement;

class LabAcheivementService
{
    public function uploadAcheivementImage($image)
    {
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadImageToS3($image, 'achievement');
            if ($uploadLabCoverImage == false) {
                return false;
            }

            return $uploadLabCoverImage;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateAcheivementImage($image)
    {
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadbase64ImageToS3($image, 'achievement');
            if ($uploadLabCoverImage == false) {
                return false;
            }
            return $uploadLabCoverImage;
        } catch (\Exception $e) {
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

    public function updateLabAchievement($lab_id, $request, $upload_acheivements_image)
    {
        try {
            $checkExistsLabAcheivement = LabAcheivement::where('lab_id', $lab_id)->first();
            if (!$checkExistsLabAcheivement) {
                $labAchievement = new LabAcheivement();
                $labAchievement->lab_id = $lab_id;
                $labAchievement->achievement_name = $request->achievement_name;
                $labAchievement->achievement_points = $request->achievement_points;
                $labAchievement->achievement_condition = $request->achievement_conditions;
                $labAchievement->achievement_image = $upload_acheivements_image;
                $labAchievement->save();
                return true;
            }
            $checkExistsLabAcheivement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $checkExistsLabAcheivement->achievement_name;
            $checkExistsLabAcheivement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $checkExistsLabAcheivement->achievement_points;
            $checkExistsLabAcheivement->achievement_condition = ($request->has('achievement_conditions')) ? $request->achievement_conditions : $checkExistsLabAcheivement->achievement_conditions;
            $checkExistsLabAcheivement->achievement_image = ($upload_acheivements_image) ? $upload_acheivements_image : $checkExistsLabAcheivement->achievement_image;
            $checkExistsLabAcheivement->save();
            return true;
        } catch (\Exception $e) {
        return false;
        }
    }

    public function deleteLabAchievement($lab_id)
    {
        try {
            $checkLabAchievementExists = LabAcheivement::where('lab_id', $lab_id)->first();
            if ($checkLabAchievementExists){
                $deleteLabAchievement = LabAcheivement::where('lab_id', $lab_id)->delete();
                if (!$deleteLabAchievement) {
                    return false;
                }
                return true;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
