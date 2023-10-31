<?php

namespace App\Repositories\Api\Public\ResourceGroup;

use App\Services\Public\ResourceGroupService;
use DB;

class ResourceGroupRepository implements ResourceGroupInterface
{
    private  $resourceGroupService;

    public function __construct(ResourceGroupService $resourceGroupService)
    {
        $this->resourceGroupService =$resourceGroupService;
    }

    public function getResourceGroupList($request)
    {
        try {
            return  $this->resourceGroupService->getResourceGroupList($request);
        }catch(\Exception $e){
            return false;
        }
    }
}
