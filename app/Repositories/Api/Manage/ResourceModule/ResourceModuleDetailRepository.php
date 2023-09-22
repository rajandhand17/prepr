<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use App\Services\Manage\ResourceModuleService;

class ResourceModuleDetailRepository implements ResourceModuleDetailInterface
{
    protected $resourceModuleService;

    protected $resourceModuleDetails;

    public function __construct(ResourceModuleService $resourceModuleService)
    {
        $this->resourceModuleService = $resourceModuleService;
    }

    public function getResourceModuleList($request)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleList($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function createResourceModule($request,$upload_media)
    {
        try {
            return  $this->resourceModuleService->createResourceModule($request,$upload_media);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function uploadResourceModuleMedia($media){
        try {
            return $this->resourceModuleService->uploadResourceModuleMedia($media);
        }catch(\Exception $e){
            return false;
        }
    }
    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return $this->resourceModuleService->checkSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($slug)
    {
        try {
            return $this->resourceModuleService->delete($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteMedia($resource_module_id)
    {
        try {
            return $this->resourceModuleService->deleteMedia($resource_module_id);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function checkName($title)
    {
        try {
            return $this->resourceModuleService->checkName($title);
        } catch(\Exception $e) {
            return false;
        }
    }
}
