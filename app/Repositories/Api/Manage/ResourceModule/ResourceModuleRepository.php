<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use App\Services\Manage\ResourceModuleService;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;

    public function __construct(ResourceModuleService $resourceModuleService)
    {
        $this->resourceModuleService = $resourceModuleService;
    }

    public function getResourceModuleList($request){
        try{
            return  $this->resourceModuleService->getResourceModuleList($request);
        }catch(\Exception $e){
            return false;
        }
    }
    public function createResourceModule($request)
    {
        try{
            return  $this->resourceModuleService->createResourceModule($request);
        }catch(\Exception $e){
            return false;
        }
    }

    public function getResourceModuleBasedOnSlug($slug){
        try{
            return $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        }catch (\Exception $e){
            return false;
        }
    }

    public function checkSlug($slug){
        try{
            return $this->resourceModuleService->checkSlug($slug);
        }catch (\Exception $e){
            return false;
        }
    }

    public function delete($slug){
        try{
            return $this->resourceModuleService->delete($slug);
        }catch (\Exception $e){
            return false;
        }
    }
}
