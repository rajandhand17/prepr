<?php

namespace App\Services\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use SebastianBergmann\ObjectEnumerator\Exception;

class ResourceReportService
{
    /**
     * @param ResourceModule $resourceModule
     *
     * @return false|array
     */
    public function getResourceModuleMemberProgress(ResourceModule $resourceModule): false|array
    {
        try {
            /**
             * LAZY LOADING.
             */
            $resourceModule->load('resourceProgress')->loadCount('resourceProgress');

            /**
             * ALL PROGRESS.
             */
            $allProgress = $resourceModule->resourceProgress()->get();

            /**
             * FILTERING AND COUNTING.
             */
            $notStarted = $allProgress->where('status', '=', '0')->count();
            $inProgress = $allProgress->where('status', '=', '1')->count();
            $completed = $allProgress->where('status', '=', '2')->count();

            return [
                'not_started' => $notStarted,
                'in_progress' => $inProgress,
                'completed'   => $completed,
                'total'       => $resourceModule->resource_progress_count,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param ResourceGroup $resourceGroup
     *
     * @return false|array
     */
    public function getResourceGroupMemberProgress(ResourceGroup $resourceGroup): false|array
    {
        try {
            /**
             * LAZY LOADING.
             */
            $resourceGroup->load('resourceGroupProgress')->loadCount('resourceGroupProgress');

            /**
             * ALL PROGRESS.
             */
            $allProgress = $resourceGroup->resourceGroupProgress()->get();

            /**
             * FILTERING AND COUNTING.
             */
            $notStarted = $allProgress->where('status', '=', '0')->count();
            $inProgress = $allProgress->where('status', '=', '1')->count();
            $completed = $allProgress->where('status', '=', '2')->count();

            return [
                'not_started' => $notStarted,
                'in_progress' => $inProgress,

                'completed' => $completed,
                'total'     => $resourceGroup->resource_group_progress_count,
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param ResourceCollection $resourceCollection
     *
     * @return false|array
     */
    public function getResourceCollectionMemberProgress(ResourceCollection $resourceCollection): false|array
    {
        try {
            /**
             * LAZY LOADING.
             */
            $resourceCollection->load('resourceCollectionProgress')->loadCount('resourceCollectionProgress');

            /**
             * ALL PROGRESS.
             */
            $allProgress = $resourceCollection->resourceCollectionProgress()->get();

            /**
             * FILTERING AND COUNTING.
             */
            $notStarted = $allProgress->where('status', '=', '0')->count();
            $inProgress = $allProgress->where('status', '=', '1')->count();
            $completed = $allProgress->where('status', '=', '2')->count();

            return [
                'not_started' => $notStarted,
                'in_progress' => $inProgress,
                'completed'   => $completed,
                'total'       => $resourceCollection->resource_collection_progress_count,
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
