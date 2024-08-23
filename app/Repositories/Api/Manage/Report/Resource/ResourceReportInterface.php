<?php

namespace App\Repositories\Api\Manage\Report\Resource;

use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;

interface ResourceReportInterface
{
    public function getResourceModuleMemberProgress(ResourceModule $resourceModule): false|array;

    public function getResourceGroupMemberProgress(ResourceGroup $resourceGroup): false|array;

    public function getResourceCollectionMemberProgress(ResourceCollection $resourceCollection): false|array;
}
