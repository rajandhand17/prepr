<?php

namespace App\Helpers;

use App\Models\ModuleCompletionStatus;
use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;
use App\Models\ResourceModuleVisit;
use App\Models\Scorm;
use App\Services\Public\ResourceModuleService;
use Exception;

class TrackUserProgressHelper
{
    /* -----------------------------------------------------------------------------------------
    @Description:  Function for getting related prepr skills
    -------------------------------------------------------------------------------------------- */
    public static function trackResourceModuleUserProgress($resourceModuleData, $userId)
    {
        try {
            // Fetch resource module assets count
            $fetchResourceModuleAssets = ResourceModuleDetail::where('resource_module_id', $resourceModuleData->id)->count();
            // Fetch resource module scorm asset count
            $scromModuleData = Scorm::where(['model_id' => $resourceModuleData->id, 'model_type' => ResourceModule::class])->count();
            // Fetch resource module go1 asset count
            $isGo1Resource = $resourceModuleData->go1_course_id ? true : false;

            // Fetch resource module overall asset count
            $moduleAssetsCount = ($fetchResourceModuleAssets + $scromModuleData) + ($isGo1Resource ? 1 : 0);
            // Fetch resource module visited overall asset count
            $totalUserVisitedModuleAssetCount = ResourceModuleVisit::where(['module_id' => $resourceModuleData->id, 'user_id' => $userId])->count();

            // Fetch resource module progress
            $moduleProgress = 0;
            if ($moduleAssetsCount > 0) {
                $moduleProgress = round($totalUserVisitedModuleAssetCount / $moduleAssetsCount * 100, 2);
            }

            // Feed resource module progress
            $moduleType = config('constants.module_type.resource_modules');
            $feedModuleProgressData = self::feedModuleProgressData($userId, $resourceModuleData->id, $moduleType, $moduleProgress);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function trackResourceCollectionUserProgress($resourceCollectionData, $userId)
    {
        try {
            if ($resourceCollectionData->resource_modules->count() > 0) {
                // Fetch resource module ids
                $resourceIds = $resourceCollectionData->resource_modules->pluck('resource_module_id');

                // Array declare for resource module assets count
                $totalResourceModuleAssetCount = [];
                $totalResourceModuleAssetCountVisited = [];

                foreach ($resourceIds->unique() as $resourceId) {
                    // Fetch resource module
                    $fetchResourceModule = ResourceModuleService::getResourceModuleBasedOnId($resourceId);

                    // Fetch resource module assets count
                    $fetchResourceModuleAssets = ResourceModuleDetail::where('resource_module_id', $fetchResourceModule->id)->count();
                    // Fetch resource module scorm asset count
                    $scromModuleData = Scorm::where(['model_id' => $fetchResourceModule->id, 'model_type' => ResourceModule::class])->count();
                    // Fetch resource module go1 asset count
                    $isGo1Resource = $fetchResourceModule->go1_course_id ? true : false;

                    // Fetch resource module overall asset count
                    $totalResourceModuleAssetCount[] = ($fetchResourceModuleAssets + $scromModuleData) + ($isGo1Resource ? 1 : 0);
                    // Fetch resource module visited overall asset count
                    $totalResourceModuleAssetCountVisited[] = ResourceModuleVisit::where(['module_id' => $fetchResourceModule->id, 'user_id' => $userId])->count();
                }

                $totalResourceModuleAsset = array_sum($totalResourceModuleAssetCount);
                $totalResourceModuleAssetVisited = array_sum($totalResourceModuleAssetCountVisited);

                // Fetch resource collection progress
                $collectionProgress = 0;
                if ($totalResourceModuleAsset > 0) {
                    $collectionProgress = round($totalResourceModuleAssetVisited / $totalResourceModuleAsset * 100, 2);
                }

                // Feed resource collection progress
                $moduleType = config('constants.module_type.resource_collections');
                $feedModuleProgressData = self::feedModuleProgressData($userId, $resourceCollectionData->id, $moduleType, $collectionProgress);
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function feedModuleProgressData($userId, $moduleId, $moduleType, $moduleProgress)
    {
        try {
            $checkModuleProgressData = ModuleCompletionStatus::where(['user_id' => $userId, 'module_id' => $moduleId, 'module_type' => $moduleType])->first();
            if ($checkModuleProgressData) {
                $feedModuleProgressData = $checkModuleProgressData;
            } else {
                $feedModuleProgressData = new ModuleCompletionStatus();
            }

            $moduleStatus = ($moduleProgress == '0') ? '0' : (($moduleProgress != '100') ? '1' : '2');
            $isModuleCompleted = ($moduleStatus == '2') ? '1' : '0';

            $feedModuleProgressData->user_id = $userId;
            $feedModuleProgressData->module_id = $moduleId;
            $feedModuleProgressData->module_type = $moduleType;
            $feedModuleProgressData->status = $moduleStatus;
            $feedModuleProgressData->is_completed = $isModuleCompleted;
            $feedModuleProgressData->percentage = $moduleProgress;
            $feedModuleProgressData->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
