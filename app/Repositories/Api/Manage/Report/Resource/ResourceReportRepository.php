<?php

namespace App\Repositories\Api\Manage\Report\Resource;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use App\Services\Manage\Report\ResourceReportService;

class ResourceReportRepository implements ResourceReportInterface
{
    public function __construct(protected ResourceReportService $resourceReportService)
    {
    }

    public function getResourceModuleMemberProgress(ResourceModule $resourceModule): false|array
    {
        try {
            return $this->resourceReportService->getResourceModuleMemberProgress($resourceModule);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getResourceGroupMemberProgress(ResourceGroup $resourceGroup): false|array
    {
        try {
            return $this->resourceReportService->getResourceGroupMemberProgress($resourceGroup);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getResourceCollectionMemberProgress(ResourceCollection $resourceCollection): false|array
    {
        try {
            return $this->resourceReportService->getResourceCollectionMemberProgress($resourceCollection);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
