<?php

namespace App\Repositories\Api\Public\ResourceGroup;

use App\Helpers\UtilityHelper;
use App\Services\Public\ResourceGroupService;
use App\Services\Public\ResourceGroupSocialActivitiesService;

class ResourceGroupRepository implements ResourceGroupInterface
{
    private $resourceGroupService;

    private $resourceGroupSocialActivitiesService;

    public function __construct(ResourceGroupService $resourceGroupService, ResourceGroupSocialActivitiesService $resourceGroupSocialActivitiesService)
    {
        $this->resourceGroupService = $resourceGroupService;
        $this->resourceGroupSocialActivitiesService = $resourceGroupSocialActivitiesService;
    }

    public function getResourceGroupList($request)
    {
        try {
            return  $this->resourceGroupService->getResourceGroupList($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getResourceGroupBasedOnSlug($slug)
    {
        try {
            return  $this->resourceGroupService->getResourceGroupBasedOnSlug($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->resourceGroupSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkSocialActivity($resource_group_id, $column, $action)
    {
        try {
            return $this->resourceGroupSocialActivitiesService->checkSocialActivity($resource_group_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function captureSocialActivity($resource_group_id, $column, $action)
    {
        try {
            return $this->resourceGroupSocialActivitiesService->captureSocialActivity($resource_group_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addRating($resource_group_id, $request)
    {
        try {
            return $this->resourceGroupService->addRating($resource_group_id, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
