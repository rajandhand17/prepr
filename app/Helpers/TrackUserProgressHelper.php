<?php

namespace App\Helpers;

use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;
use App\Models\ResourceModuleVisit;
use App\Models\Scorm;
use App\Services\ModuleCompletionStatusService;
use App\Services\ProjectService;
use App\Services\Public\ChallengePathService;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabService;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceGroupService;
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
            $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $resourceModuleData->id, $moduleType, $moduleProgress);

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
                $getUserProgressBasedOnResourceModuleIds = self::getUserProgressBasedOnResourceModuleIds($resourceIds, $userId);

                // Feed resource collection progress
                $moduleType = config('constants.module_type.resource_collections');
                $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $resourceCollectionData->id, $moduleType, $getUserProgressBasedOnResourceModuleIds);
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function trackResourceGroupUserProgress($resourceGroupData, $userId)
    {
        try {
            $resourceModuleIds = collect();
            $resourceCollectionIds = collect();
            if ($resourceGroupData->resource_modules->count() > 0) {
                // Fetch resource module ids
                $resourceModuleIds = $resourceGroupData->resource_modules->pluck('resource_module_id');
            }

            if ($resourceGroupData->resource_collection->count() > 0) {
                // Fetch resource collection ids
                $collectionIds = $resourceGroupData->resource_collection->pluck('resource_collection_id');
                $fetchResourceCollections = ResourceCollectionService::getResourceCollectionBasedOnArrayIds($collectionIds);

                // Fetch resource module id based on resource collection ids
                foreach ($fetchResourceCollections as $resourceCollection) {
                    $resourceIds = $resourceCollection->resource_modules->pluck('resource_module_id');
                    $resourceCollectionIds = $resourceCollectionIds->merge($resourceIds);
                }
            }

            // Merge combined resource module ids of resource group
            $combinedResourceModuleIds = $resourceModuleIds->merge($resourceCollectionIds);
            $getUserProgressBasedOnResourceModuleIds = self::getUserProgressBasedOnResourceModuleIds($combinedResourceModuleIds, $userId);

            // Feed resource group progress
            $moduleType = config('constants.module_type.resource_group');
            $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $resourceGroupData->id, $moduleType, $getUserProgressBasedOnResourceModuleIds);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserProgressBasedOnResourceModuleIds($resourceIds, $userId)
    {
        try {
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
            $userProgressBasedOnResourceIds = 0;
            if ($totalResourceModuleAsset > 0) {
                $userProgressBasedOnResourceIds = round($totalResourceModuleAssetVisited / $totalResourceModuleAsset * 100, 2);
            }

            return $userProgressBasedOnResourceIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function trackChallengeUserProgress($challengeData, $userId)
    {
        try {
            // Default Challenge Progress
            $getUserChallengeProgress = '0';
            // Check is the user has joined the challenge or not
            $joined_status = $challengeData->joined();
            if ($joined_status != 'NA' && $joined_status != null) {
                if ($joined_status->invite_status == '1') {
                    // Check the Project status based on Challenge and UserId
                    $checkUserChallengeStatus = ProjectService::checkUserChallengeStatus($challengeData->id, $userId);
                    if ($getUserChallengeProgress != null) {
                        switch ($checkUserChallengeStatus->is_submitted) {
                            case '0':
                                $getUserChallengeProgress = '50';
                                break;
                            case '1':
                                $getUserChallengeProgress = '100';
                                break;
                        }
                    }
                }
            }

            // Feed challenge progress
            $moduleType = config('constants.module_type.challenges');
            $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $challengeData->id, $moduleType, $getUserChallengeProgress);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function trackChallengePathUserProgress($challengePathData, $userId)
    {
        try {
            $totalChallengeCount = 0;
            $completedChallengeCount = 0;
            if ($challengePathData->challenges->count() > 0) {
                $totalChallengeCount = $challengePathData->challenges->count();
                $challengeIds = $challengePathData->challenges->pluck('challenge_id');
                $getChallengeBasedOnIds = ChallengeService::getChallengeBasedOnArrayIds($challengeIds);
                if ($getChallengeBasedOnIds->isNotEmpty()) {
                    foreach ($getChallengeBasedOnIds as $challengeData) {
                        // Check is the user has joined the challenge or not
                        $joined_status = $challengeData->joined();
                        if ($joined_status != 'NA' && $joined_status != null) {
                            if ($joined_status->invite_status == '1') {
                                // Check User Joined Challenge or not to get progress
                                $checkUserChallengeStatus = ProjectService::checkUserChallengeStatus($challengeData->id, $userId);
                                if ($checkUserChallengeStatus != null) {
                                    if ($checkUserChallengeStatus->is_submitted == '0') {
                                        // If created project but not submitted so setting progress as 50% completion
                                        $completedChallengeCount += 0.5;
                                    } elseif ($checkUserChallengeStatus->is_submitted == '1') {
                                        // If created project and submitted so setting progress as 100% completion
                                        $completedChallengeCount++;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Fetch challenge path progress user
            $challengePathProgress = 0;
            if ($totalChallengeCount > 0) {
                $challengePathProgress = round($completedChallengeCount / $totalChallengeCount * 100, 2);
            }

            // Feed challenge path progress
            $moduleType = config('constants.module_type.challenge_paths');
            $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $challengePathData->id, $moduleType, $challengePathProgress);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function trackLabUserProgress($lab, $userId)
    {
        try {
            // Check is the user has joined the lab or not
            $joined_status = $lab->joined();
            if ($joined_status != 'NA' && $joined_status != null) {
                if ($joined_status->invite_status == '1') {
                    // Initialize default associated count
                    $labChallengeAssociation = 0;
                    $labChallengePathAssociation = 0;
                    $labResourceModuleAssociation = 0;
                    $labResourceCollectionAssociation = 0;
                    $labResourceGroupAssociation = 0;

                    // Initialize default completed associated count
                    $competedLabChallengeAssociation = 0;
                    $competedLabChallengePathAssociation = 0;
                    $competedLabResourceModuleAssociation = 0;
                    $competedLabResourceCollectionAssociation = 0;
                    $competedLabResourceGroupAssociation = 0;

                    if ($lab->lab_challenge_association->count() > 0) {
                        $labChallengeAssociation = $lab->lab_challenge_association->count();
                        $challengeIds = $lab->lab_challenge_association->pluck('challenge_id');

                        // Fetch Completed Challenge Count
                        $competedLabChallengeAssociation = self::getUserProgressBasedOnChallengeIds($challengeIds, $userId);
                    }

                    if ($lab->lab_challenge_path_association->count() > 0) {
                        $challengePathIds = collect();
                        $pathIds = $lab->lab_challenge_path_association->pluck('challenge_path_id');
                        $fetchChallengePaths = ChallengePathService::getChallengePathBasedOnArrayIds($pathIds);

                        // Fetch challenge id based on challenge path ids
                        foreach ($fetchChallengePaths as $challengePath) {
                            $challengeIds = $challengePath->challenges->pluck('challenge_id');
                            $challengePathIds = $challengePathIds->merge($challengeIds);
                        }

                        $labChallengePathAssociation = $challengePathIds->count();
                        // Fetch Completed Challenge Count
                        $competedLabChallengePathAssociation = self::getUserProgressBasedOnChallengeIds($challengePathIds, $userId);
                    }

                    if ($lab->lab_resource_module_association->count() > 0) {
                        $resourceModuleIds = $lab->lab_resource_module_association->pluck('resource_module_id');

                        // Fetch resource module assets counts with visited counts
                        $getResourceModuleAssetCount = self::getModuleAssetCountsBasedOnResourceModuleIds($resourceModuleIds, $userId);
                        $labResourceModuleAssociation = $getResourceModuleAssetCount['totalResourceModuleAsset'];
                        $competedLabResourceModuleAssociation = $getResourceModuleAssetCount['totalResourceModuleAssetVisited'];
                    }

                    if ($lab->lab_resource_collection_association->count() > 0) {
                        $resourceCollectionIds = collect();
                        // Fetch resource collection ids
                        $collectionIds = $lab->lab_resource_collection_association->pluck('resource_collection_id');
                        $fetchResourceCollections = ResourceCollectionService::getResourceCollectionBasedOnArrayIds($collectionIds);

                        // Fetch resource module id based on resource collection ids
                        foreach ($fetchResourceCollections as $resourceCollection) {
                            $resourceIds = $resourceCollection->resource_modules->pluck('resource_module_id');
                            $resourceCollectionIds = $resourceCollectionIds->merge($resourceIds);
                        }

                        // Fetch resource module assets counts with visited counts
                        $getResourceCollectionAssetCount = self::getModuleAssetCountsBasedOnResourceModuleIds($resourceModuleIds, $userId);
                        $labResourceCollectionAssociation = $getResourceCollectionAssetCount['totalResourceModuleAsset'];
                        $competedLabResourceCollectionAssociation = $getResourceCollectionAssetCount['totalResourceModuleAssetVisited'];
                    }

                    if ($lab->lab_resource_group_association->count() > 0) {
                        $resourceGroupIds = $lab->lab_resource_group_association->pluck('resource_group_id');
                        $fetchResourceGroups = ResourceGroupService::getResourceGroupBasedOnArrayIds($resourceGroupIds);

                        // Initialize default collection array
                        $labResourceModuleIds = collect();
                        $labResourceCollectionIds = collect();

                        // Check fetched resource groups are not empty
                        if ($fetchResourceGroups->isNotEmpty()) {
                            foreach ($fetchResourceGroups as $resourceGroup) {
                                // Fetch resource module ids based on lab associated resource groups ids
                                if ($resourceGroup->resource_modules->count() > 0) {
                                    $moduleIds = $resourceGroup->resource_modules->pluck('resource_module_id');
                                    $labResourceModuleIds = $labResourceModuleIds->merge($moduleIds);
                                }

                                // Fetch resource module ids based on lab associated resource groups id via resource collections
                                if ($resourceGroup->resource_collection->count() > 0) {
                                    $collectionIds = $resourceGroup->resource_collection->pluck('resource_collection_id');
                                    $fetchResourceCollections = ResourceCollectionService::getResourceCollectionBasedOnArrayIds($collectionIds);

                                    // Fetch resource module id based on resource collection ids
                                    foreach ($fetchResourceCollections as $resourceCollection) {
                                        $resourceIds = $resourceCollection->resource_modules->pluck('resource_module_id');
                                        $labResourceCollectionIds = $labResourceCollectionIds->merge($resourceIds);
                                    }
                                }
                            }
                        }

                        $resourceModuleIds = $labResourceModuleIds->merge($labResourceCollectionIds);

                        // Fetch resource module assets counts with visited counts
                        $getResourceGroupAssetCount = self::getModuleAssetCountsBasedOnResourceModuleIds($resourceModuleIds, $userId);
                        $labResourceGroupAssociation = $getResourceGroupAssetCount['totalResourceModuleAsset'];
                        $competedLabResourceGroupAssociation = $getResourceGroupAssetCount['totalResourceModuleAssetVisited'];
                    }

                    $totalLabAssociatedData = ($labChallengeAssociation + $labChallengePathAssociation + $labResourceModuleAssociation + $labResourceCollectionAssociation + $labResourceGroupAssociation);
                    $totalLabCompletedAssociatedData = ($competedLabChallengeAssociation + $competedLabChallengePathAssociation + $competedLabResourceModuleAssociation + $competedLabResourceCollectionAssociation + $competedLabResourceGroupAssociation);

                    // Fetch challenge path progress user
                    $labProgress = 0;
                    if ($totalLabAssociatedData > 0) {
                        $labProgress = round($totalLabCompletedAssociatedData / $totalLabAssociatedData * 100, 2);
                    }

                    // Feed challenge path progress
                    $moduleType = config('constants.module_type.labs');
                    $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $lab->id, $moduleType, $labProgress);
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserProgressBasedOnChallengeIds($challengeIds, $userId)
    {
        try {
            // Initialize default challenge count
            $competedLabChallengeAssociation = 0;

            $getChallengeBasedOnIds = ChallengeService::getChallengeBasedOnArrayIds($challengeIds);
            if ($getChallengeBasedOnIds->isNotEmpty()) {
                foreach ($getChallengeBasedOnIds as $challengeData) {
                    // Check is the user has joined the challenge or not
                    $joined_status = $challengeData->joined();
                    if ($joined_status != 'NA' && $joined_status != null) {
                        if ($joined_status->invite_status == '1') {
                            // Check User Joined Challenge or not to get progress
                            $checkUserChallengeStatus = ProjectService::checkUserChallengeStatus($challengeData->id, $userId);
                            if ($checkUserChallengeStatus != null) {
                                if ($checkUserChallengeStatus->is_submitted == '0') {
                                    // If created project but not submitted so setting progress as 50% completion
                                    $competedLabChallengeAssociation += 0.5;
                                } elseif ($checkUserChallengeStatus->is_submitted == '1') {
                                    // If created project and submitted so setting progress as 100% completion
                                    $competedLabChallengeAssociation++;
                                }
                            }
                        }
                    }
                }
            }

            return $competedLabChallengeAssociation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getModuleAssetCountsBasedOnResourceModuleIds($resourceIds, $userId)
    {
        try {
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
            $userProgressBasedOnResourceIds = ['totalResourceModuleAsset' => $totalResourceModuleAsset, 'totalResourceModuleAssetVisited' => $totalResourceModuleAssetVisited];

            return $userProgressBasedOnResourceIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function trackLabProgramUserProgress($labProgramData, $userId)
    {
        try {
            $fetchTotalLabs = 0;
            $fetchTotalUserLabProgress = 0;
            if ($labProgramData->labs->count() > 0) {
                $labIds = $labProgramData->labs->pluck('lab_id');
                $labDatas = LabService::getLabsBasedOnIds($labIds);
                if ($labDatas->isNotEmpty()) {
                    // Updating lab progress based on associated to lab program
                    foreach ($labDatas as $lab) {
                        $updateLabProgress = self::trackLabUserProgress($lab, $userId);
                    }

                    // Total lab multiplied with 100 and only progress of lab is plucked
                    $fetchTotalLabs = 100 * $labDatas->count();
                    $fetchUserLabProgress = ModuleCompletionStatusService::getLabUserProgressBasedOnLabsAndUserIds($labDatas->pluck('id'), $userId);
                    $fetchTotalUserLabProgress = $fetchUserLabProgress->pluck('percentage')->sum();
                }
            }

            // Fetch lab program progress user
            $labProgramProgress = 0;
            if ($fetchTotalLabs > 0) {
                $labProgramProgress = round($fetchTotalUserLabProgress / $fetchTotalLabs * 100, 2);
            }

            // Feed lab program progress
            $moduleType = config('constants.module_type.lab_programs');
            $feedModuleProgressData = ModuleCompletionStatusService::feedModuleProgressData($userId, $labProgramData->id, $moduleType, $labProgramProgress);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
