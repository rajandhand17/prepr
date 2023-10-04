<?php

namespace App\Repositories\Api\Public\ResourceModule;

use App\Services\Public\ResourceModuleRatingService;
use App\Services\Public\ResourceModuleService;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;
    protected $resourceModuleRatingService;
    public function __construct(ResourceModuleService $resourceModuleService, ResourceModuleRatingService $resourceModuleRatingService)
    {
        $this->resourceModuleService = $resourceModuleService;
        $this->resourceModuleRatingService=$resourceModuleRatingService;
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

    public function checkslug($slug){
        try {
            return $this->resourceModuleService->checkslug($slug);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function checkReview($resource_module_id,$request){
        try {
            return $this->resourceModuleService->checkReview($resource_module_id,$request);
        }catch (\Exception $e) {
            return false;
        }
    }
    public function addReview($resource_module_id,$request){
        try {
           return $this->resourceModuleRatingService->addReview($resource_module_id,$request);
        }catch(\exception $e) {
            return false;
        }
    }
}
