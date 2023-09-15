<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use App\Services\Manage\ResourceModuleService;

class ResourceModuleRepository implements ResourceModuleInterface
{
    protected $resourceModuleService;

    public function __construct(ResourceModuleService $resourceModuleService)
    {
        $this->$resourceModuleService = $resourceModuleService;
    }

    public function createResourceModule($request)
    {
        return  $this->resourceModuleService->createResourceModule($request);

    }
}
