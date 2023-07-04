<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\LabAcheivement;
use DB;
class LabAcheivementService
{
    public function uploadAcheivementImage($image){
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadImageToS3($image,"achievement");
            if ($uploadLabCoverImage == false) {
                return false;
            }
            return $uploadLabCoverImage;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createLabAchievement($request,$lab,$upload_acheivements_image){
        try {

            $labAchievement=new LabAcheivement();
            $labAchievement->lab_id=$lab->id;
            $labAchievement->achievement_name=$request->achievement_name;
            $labAchievement->achievement_points=$request->achievement_points;
            $labAchievement->achievement_condition=$request->achievement_conditions;
            $labAchievement->achievement_image=$upload_acheivements_image;
            $labAchievement->save();
            return true;
        } catch (\Exception $e){
            return false;
        }
    }
}
