<?php

namespace App\Console\Commands\ModuleProgressStatus;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ResourceGroupService;
use App\Services\ModuleCompletionStatusService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResourceGroupProgressStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-progress:resource-group-progress-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to check users progress in resource group';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Started to check users progress in resource group');
            DB::beginTransaction();
            $componentType = 'resource-group';
            $fetchComponentDataProgress = ModuleCompletionStatusService::fetchComponentDataProgress($componentType);
            if ($fetchComponentDataProgress->isNotEmpty()) {
                foreach ($fetchComponentDataProgress as $resourceGroupProgress) {
                    $fetchResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($resourceGroupProgress->module_id);
                    if ($fetchResourceGroup) {
                        TrackUserProgressHelper::trackResourceGroupUserProgress($fetchResourceGroup, $resourceGroupProgress->user_id);
                    }
                }
            }
            $this->info('Completed to check users progress in resource group');

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            DB::rollback();
            $this->error('Resource group status not updated');
        }
    }
}
