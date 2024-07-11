<?php

namespace App\Helpers;

use App\Models\ModuleCompletionStatus;
use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;
use App\Models\ResourceModuleVisit;
use App\Models\Scorm;
use Exception;

class TrackUserProgressHelper
{
    /* -----------------------------------------------------------------------------------------
    @Description:  Function for getting related prepr skills
    -------------------------------------------------------------------------------------------- */
    public static function trackResourceModuleUserProgress($resourceData, $userId)
    {
        try {
            $fetchResourceModuleAssets = ResourceModuleDetail::where('resource_module_id', $resourceData->id)->count();
            $scromModuleData = Scorm::where(['model_id' => $resourceData->id, 'model_type' => ResourceModule::class])->count();
            $isGo1Resource = $resourceData->go1_course_id ? true : false;

            $moduleAssetsCount = ($fetchResourceModuleAssets + $scromModuleData) + ($isGo1Resource ? 1 : 0);

            $totalUserVisitedModuleAssetCount = ResourceModuleVisit::where(['module_id' => $resourceData->id, 'user_id' => $userId])->count();

            $moduleProgress = 0;
            if ($moduleAssetsCount > 0) {
                $moduleProgress = round($totalUserVisitedModuleAssetCount / $moduleAssetsCount * 100, 2);
            }

            $moduleType = config('constants.module_type.resource_modules');
            $feedModuleProgressData = self::feedModuleProgressData($userId, $resourceData->id, $moduleType, $moduleProgress);

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
