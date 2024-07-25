<?php

namespace App\Console\Commands\ModuleProgressStatus;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ResourceModuleService;
use App\Services\ModuleCompletionStatusService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResourceModuleProgressStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-progress:resource-module-progress-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to check users progress in resource module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Command started to check users progress in resource module');
            DB::beginTransaction();
            $componentType = 'resource-module';
            $fetchComponentDataProgress = ModuleCompletionStatusService::fetchComponentDataProgress($componentType);
            if ($fetchComponentDataProgress->isNotEmpty()) {
                foreach ($fetchComponentDataProgress as $resourceModuleProgress) {
                    $fetchResourceModule = ResourceModuleService::getResourceModulesBasedOnId($resourceModuleProgress->module_id);
                    if ($fetchResourceModule) {
                        TrackUserProgressHelper::trackResourceModuleUserProgress($fetchResourceModule, $resourceModuleProgress->user_id);
                    }
                }
            }
            $this->info('Command completed to check users progress in resource module');

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            DB::rollback();
            $this->error('Resource module status not updated');
        }
    }
}
