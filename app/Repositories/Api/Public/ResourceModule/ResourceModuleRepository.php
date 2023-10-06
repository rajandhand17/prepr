<?php

namespace App\Repositories\Api\Public\ResourceModule;

use App\Services\Public\ResourceModuleRatingService;
use App\Services\Public\ResourceModuleService;
use App\Services\Public\ResourceModuleSocialActivitiesService;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;
    protected $resourceModuleRatingService;

    protected $resourceModuleSocialActivitiesService;

    public function __construct(ResourceModuleService $resourceModuleService, ResourceModuleRatingService $resourceModuleRatingService, ResourceModuleSocialActivitiesService $resourceModuleSocialActivitiesService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleRatingService = $resourceModuleRatingService;
        $this->resourceModuleSocialActivitiesService=$resourceModuleSocialActivitiesService;
    }

    public function getResourceModuleList($request)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleList($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function checkslug($slug)
    {
        try {
            return $this->resourceModuleService->checkslug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkReview($resource_module_id, $request)
    {
        try {
            return $this->resourceModuleService->checkReview($resource_module_id, $request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addRating($resource_module_id, $request)
    {
        try {
            return $this->resourceModuleRatingService->addRating($resource_module_id, $request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function socialActivity($resource_module_id,$request){
        try{
            return $this->resourceModuleSocialActivitiesService->socialActivity($resource_module_id,$request);
        }catch(\Exception $e){
            return false;
        }
    }

    public function getColumnNameValue($action){
        try{
            return $this->resourceModuleSocialActivitiesService->getColumnNameValue($action);
        }catch(\Exception $e){
            return false;
        }
    }

    public function checkSocialActivity($resource_module_id,$column, $action){
        try{
            return $this->resourceModuleSocialActivitiesService->checkSocialActivity($resource_module_id,$column, $action);
        }catch(\Exception $e){
            return false;
        }
    }

    public function captureSocialActivity($resource_module_id, $column, $action){
        try{
            return $this->resourceModuleSocialActivitiesService->captureSocialActivity($resource_module_id,$column, $action);

        }catch(\Exception $e){
            return false;
        }
    }
}
