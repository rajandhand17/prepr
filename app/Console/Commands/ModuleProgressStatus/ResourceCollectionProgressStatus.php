<?php

namespace App\Console\Commands\ModuleProgressStatus;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ResourceCollectionService;
use App\Services\ModuleCompletionStatusService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResourceCollectionProgressStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-progress:resource-collection-progress-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to check users progress in resource collection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Started to check users progress in resource collection');
            DB::beginTransaction();
            $componentType = 'resource-collection';
            $fetchComponentDataProgress = ModuleCompletionStatusService::fetchComponentDataProgress($componentType);
            if ($fetchComponentDataProgress->isNotEmpty()) {
                foreach ($fetchComponentDataProgress as $resourceCollectionProgress) {
                    $fetchResourceCollection = ResourceCollectionService::getResourceCollectionsBasedOnId($resourceCollectionProgress->module_id);
                    if ($fetchResourceCollection) {
                        TrackUserProgressHelper::trackResourceCollectionUserProgress($fetchResourceCollection, $resourceCollectionProgress->user_id);
                    }
                }
            }
            $this->info('Completed to check users progress in resource collection');

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            DB::rollback();
            $this->error('Resource collection status not updated');
        }
    }
}
