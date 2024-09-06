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

            return $feedModuleProgressData;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchComponentDataProgress($componentType)
    {
        try {
            switch ($componentType) {
                case 'lab':
                    $componentId = '0';
                    break;
                case 'lab-program':
                    $componentId = '1';
                    break;
                case 'challenge':
                    $componentId = '2';
                    break;
                case 'challenge-path':
                    $componentId = '3';
                    break;
                case 'resource-module':
                    $componentId = '4';
                    break;
                case 'resource-collection':
                    $componentId = '5';
                    break;
                case 'resource-group':
                    $componentId = '6';
                    break;
            }

            $fetchComponentDataProgress = ModuleCompletionStatus::where('module_type', $componentId)->get();

            return $fetchComponentDataProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceProgress($moduleType, $status)
    {
        try {
            $checkChallengePathCompleted = ModuleCompletionStatus::where([
                'module_type'   => $moduleType,
                'status'        => $status,
                'user_id'       => auth()->user()->id,
            ])->get();

            return $checkChallengePathCompleted;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchResourceModuleIdsBasedOnProgress($userData)
    {
        try {
            $fetchResourceModuleIdsBasedOnProgress = ModuleCompletionStatus::where(['user_id' => $userData->id, 'module_type' => '4'])->where('percentage', '<>', 0)->pluck('module_id');

            return $fetchResourceModuleIdsBasedOnProgress->unique();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchUserLabProgressBasedOnLabids($labIds, $userData)
    {
        try {
            $fetchUserLabProgressBasedOnLabids = ModuleCompletionStatus::whereIn('module_id', $labIds)->where(['module_type' => '0', 'user_id' => $userData->id])->get();

            return $fetchUserLabProgressBasedOnLabids;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchResourceModuleBasedOnUserId($userData)
    {
        try {
            $fetchResourceModuleBasedOnUserId = ModuleCompletionStatus::where(['module_type' => '4', 'user_id' => $userData->id])->get();

            return $fetchResourceModuleBasedOnUserId;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function totalViewersCountBasedOnResourceModuleIds($resouceModuleIds)
    {
        try {
            $totalViewersCountBasedOnResourceModuleIds = ModuleCompletionStatus::whereIn('module_id', $resouceModuleIds)->where('module_type', '4')->count();

            return $totalViewersCountBasedOnResourceModuleIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchModuleIdBasedProgress($moduleId, $moduleType, $userId)
    {
        try {
            $fetchComponentIdBasedProgress = ModuleCompletionStatus::where(['module_id' => $moduleId, 'module_type' => $moduleType, 'user_id' => $userId])->first();

            return $fetchComponentIdBasedProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengePathCompletedBasedOnIds($challengePathIds, $userId)
    {
        try {
            $fetchChallengePathCompletedBasedOnIds = ModuleCompletionStatus::where([
                'user_id'       => $userId,
                'module_type'   => '3',
                'is_completed'  => '1',
            ])->whereIn('module_id', $challengePathIds)->get();

            return $fetchChallengePathCompletedBasedOnIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchResourceModuleCompletedBasedOnIds($resourceModuleIds, $userId)
    {
        try {
            $checkResourceModuleProgress = ModuleCompletionStatus::where([
                'user_id'           => $userId,
                'module_type'       => '4',
                'percentage'        => '100',
            ])->whereIn('module_id', $resourceModuleIds)->get();

            return $checkResourceModuleProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchResourceCollectionCompletedBasedOnIds($resourceCollectionIds, $userId)
    {
        try {
            $checkResourceCollectionProgress = ModuleCompletionStatus::where([
                'user_id'           => $userId,
                'module_type'       => '5',
                'percentage'        => '100',
            ])->whereIn('module_id', $resourceCollectionIds)->get();

            return $checkResourceCollectionProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchResourceGroupCompletedBasedOnIds($resourceGroupIds, $userId)
    {
        try {
            $checkResourceGroupProgress = ModuleCompletionStatus::where([
                'user_id'       => $userId,
                'module_type'   => '6',
                'is_completed'  => '1',
            ])->whereIn('module_id', $resourceGroupIds)->get();

            return $checkResourceGroupProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchLabCompletedBasedOnIds($labIds, $userId)
    {
        try {
            $checkResourceGroupProgress = ModuleCompletionStatus::where([
                'user_id'       => $userId,
                'module_type'   => '0',
                'is_completed'  => '1',
            ])->whereIn('module_id', $labIds)->get();

            return $checkResourceGroupProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchComponentProgressBasedOnIds($componentIds, $moduleType, $moduleStatus, $userIds)
    {
        try {
            $checkResourceGroupProgress = ModuleCompletionStatus::where([
                'status'        => $moduleStatus,
                'module_type'   => $moduleType,
            ])->whereIn('module_id', $componentIds)->whereIn('user_id', $userIds)->get();

            return $checkResourceGroupProgress;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
