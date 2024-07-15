<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ModuleCompletionStatus;
use Exception;

class ModuleCompletionStatusService
{
    public static function checkChallengePathAchievementAssignedOrNot($challengePathId, $userId)
    {
        try {
            $checkChallengePathAchievementAssignedOrNot = ModuleCompletionStatus::where([
                'module_id'     => $challengePathId,
                'user_id'       => $userId,
                'module_type'   => '3',
                'status'        => '2',
                'is_completed'  => '1',
            ])->exists();

            return $checkChallengePathAchievementAssignedOrNot;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function markChallengePathCompleted($challengePathId, $userId)
    {
        try {
            $checkChallengePathCompleted = ModuleCompletionStatus::where([
                'module_id'     => $challengePathId,
                'user_id'       => $userId,
                'module_type'   => '3',
            ])->first();

            if ($checkChallengePathCompleted) {
                $markChallengePathCompleted = $checkChallengePathCompleted;
            } else {
                $markChallengePathCompleted = new ModuleCompletionStatus();
            }

            $markChallengePathCompleted->module_id = $challengePathId;
            $markChallengePathCompleted->user_id = $userId;
            $markChallengePathCompleted->module_type = '3';
            $markChallengePathCompleted->status = '2';
            $markChallengePathCompleted->is_completed = '1';
            $markChallengePathCompleted->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabUserProgressBasedOnLabsAndUserIds($labIds, $userId)
    {
        try {
            $labUserProgress = ModuleCompletionStatus::whereIn('module_id', $labIds)->where(['user_id' => $userId, 'module_type' => '0'])->get();

            return $labUserProgress;
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
