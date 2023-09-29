<?php

namespace App\Repositories\Api\Public\ResourceModule;

use App\Services\Public\ResourceModuleService;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;

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

    public function getResourceModuleBasedOnSlug($slug)
    {
        try {
            return  $this->resourceModuleService->getResourceModuleBasedOnSlug($slug);
        } catch(\Exception $e) {
            return false;
        }
    }
}
