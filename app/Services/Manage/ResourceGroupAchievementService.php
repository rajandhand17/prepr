<?php

namespace App\Services\Manage;


use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\LabProgramsAchievement;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceGroupAchievement;
use HiFolks\RandoPhp\Randomize;

class ResourceGroupAchievementService
{
    public function uploadAchievementImage($image)
    {
        try {
            $upload_Achievement_image = FileUploadHelper::uploadImageToS3($image, 'resource_group_achievement');
            if ($upload_Achievement_image == false) {
                return false;
            }
            return $upload_Achievement_image;
        } catch (\Exception $e) {
            return false;
        }
    }
    public static function createResourceGroupsAchievements($request,$upload_achievement_image, $resource_group_id){
        try{
            $resourceGroupAchievement = new ResourceGroupAchievement();
            $resourceGroupAchievement->resource_group_id = $resource_group_id;
            $resourceGroupAchievement->achievement_name = $request->achievement_name;
            $resourceGroupAchievement->achievement_points = $request->achievement_points;
            $resourceGroupAchievement->achievement_image = $upload_achievement_image;
            $resourceGroupAchievement->save();
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

}
