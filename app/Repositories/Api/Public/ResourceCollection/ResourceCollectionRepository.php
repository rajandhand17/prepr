<?php

namespace App\Repositories\Api\Public\ResourceCollection;

use App\Helpers\UtilityHelper;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceCollectionSocialActivitiesService;

class ResourceCollectionRepository implements ResourceCollectionInterface
{
    private $resourceCollectionService;

    private $resourceCollectionSocialActivity;

    public function __construct(ResourceCollectionService $resourceCollectionService, ResourceCollectionSocialActivitiesService $resourceCollectionSocialActivity)
    {
        $this->resourceCollectionService = $resourceCollectionService;
        $this->resourceCollectionSocialActivity = $resourceCollectionSocialActivity;
    }

    public function getResourceCollectionList($request)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getResourceCollectionBasedOnSlug($slug)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionBasedOnSlug($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->resourceCollectionSocialActivity->getColumnNameValue($action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkSocialActivity($resource_collection_id, $column, $action)
    {
        try {
            return $this->resourceCollectionSocialActivity->checkSocialActivity($resource_collection_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function captureSocialActivity($resource_collection_id, $column, $action)
    {
        try {
            return $this->resourceCollectionSocialActivity->captureSocialActivity($resource_collection_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addRating($resource_collection_id, $request)
    {
        try {
            return $this->resourceCollectionSocialActivity->addRating($resource_collection_id, $request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
