<?php

namespace App\Repositories\Api\Public\ResourceModule;

use App\Helpers\UtilityHelper;
use App\Services\Public\ResourceModuleDetailService;
use App\Services\Public\ResourceModuleRatingService;
use App\Services\Public\ResourceModuleService;
use App\Services\Public\ResourceModuleSocialActivitiesService;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;
    protected $resourceModuleRatingService;
    protected $resourceModuleSocialActivitiesService;
    protected $resourceModuleDetailService;

    public function __construct(ResourceModuleService $resourceModuleService, ResourceModuleRatingService $resourceModuleRatingService, ResourceModuleSocialActivitiesService $resourceModuleSocialActivitiesService, ResourceModuleDetailService $resourceModuleDetailService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleRatingService = $resourceModuleRatingService;
        $this->resourceModuleSocialActivitiesService = $resourceModuleSocialActivitiesService;
        $this->resourceModuleDetailService = $resourceModuleDetailService;
    }

    public function getResourceModuleList($request)
    {
        try {
            return $this->resourceModuleService->getResourceModuleList($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function addRating($resource_module_id, $request)
    {
        try {
            return $this->resourceModuleRatingService->addRating($resource_module_id, $request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->resourceModuleSocialActivitiesService->getColumnNameValue($action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSocialActivity($resource_module_id, $column, $action)
    {
        try {
            return $this->resourceModuleSocialActivitiesService->checkSocialActivity($resource_module_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($resource_module_id, $column, $action)
    {
        try {
            return $this->resourceModuleSocialActivitiesService->captureSocialActivity($resource_module_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkResourceModuleAsset($resourceModuleId, $assetId)
    {
        try {
            return $this->resourceModuleDetailService->checkResourceModuleAsset($resourceModuleId, $assetId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType)
    {
        try {
            return $this->resourceModuleDetailService->checkResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function addResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType)
    {
        try {
            return $this->resourceModuleDetailService->addResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
