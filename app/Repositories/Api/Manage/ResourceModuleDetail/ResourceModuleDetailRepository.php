<?php

namespace App\Repositories\Api\Manage\ResourceModuleDetail;

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

}
