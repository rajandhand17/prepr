<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createResourceGroupsAchievements($request, $upload_achievement_image, $resource_group_id)
    {
        try {
            $resourceGroupAchievement = new ResourceGroupAchievement();
            $resourceGroupAchievement->resource_group_id = $resource_group_id;
            $resourceGroupAchievement->achievement_name = $request->achievement_name;
            $resourceGroupAchievement->achievement_points = $request->achievement_points;
            $resourceGroupAchievement->achievement_image = $upload_achievement_image;
            $resourceGroupAchievement->save();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceGroupAchievements($resourceGroupId)
    {
        try {
            $resourceGroupAchievements = ResourceGroupAchievement::where('resource_group_id', $resourceGroupId)->delete();
            if ($resourceGroupAchievements) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceGroupsAchievements($request, $upload_achievement_image, $updateResourceGroupId)
    {
        try {
            $checkExistsResourceGroupAchievement = ResourceGroupAchievement::where('resource_group_id', $updateResourceGroupId)->first();
            if (!$checkExistsResourceGroupAchievement) {
                $resourceGroupAchievement = new ResourceGroupAchievement();
                $resourceGroupAchievement->resource_group_id = $updateResourceGroupId;
                $resourceGroupAchievement->achievement_name = $request->achievement_name;
                $resourceGroupAchievement->achievement_points = $request->achievement_points;
                $resourceGroupAchievement->achievement_image = $upload_achievement_image;
                $resourceGroupAchievement->save();

                return true;
            }
            $checkExistsResourceGroupAchievement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $checkExistsResourceGroupAchievement->achievement_name;
            $checkExistsResourceGroupAchievement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $checkExistsResourceGroupAchievement->achievement_points;
            $checkExistsResourceGroupAchievement->achievement_image = ($upload_achievement_image) ? $upload_achievement_image : $checkExistsResourceGroupAchievement->achievement_image;
            $checkExistsResourceGroupAchievement->save();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceGroupsAchievements($originalResourceGroupAssociation, $clonedResourceGroupId){
        try {
            $resourceGroup = new ResourceGroupAchievement();
            $resourceGroup = $originalResourceGroupAssociation->replicate();
            $resourceGroup->resource_group_id = $clonedResourceGroupId;
            $resourceGroup->save();
            return true;
        }catch(\Exception $e){
            UtilityHelper::logError($e);
            return false;
        }
    }
}
