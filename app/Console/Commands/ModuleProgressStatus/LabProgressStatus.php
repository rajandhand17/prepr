<?php

namespace App\Console\Commands\ModuleProgressStatus;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Services\Manage\LabService;
use App\Services\ModuleCompletionStatusService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LabProgressStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-progress:lab-progress-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to check users progress in lab';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Started to check users progress in lab');
            DB::beginTransaction();
            $componentType = 'lab';
            $fetchComponentDataProgress = ModuleCompletionStatusService::fetchComponentDataProgress($componentType);
            if ($fetchComponentDataProgress->isNotEmpty()) {
                foreach ($fetchComponentDataProgress as $labProgress) {
                    $fetchLab = LabService::getLabBasedOnId($labProgress->module_id);
                    if ($fetchLab) {
                        TrackUserProgressHelper::trackLabUserProgress($fetchLab, $labProgress->user_id);
                    }
                }
            }
            $this->info('Completed to check users progress in lab');

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            DB::rollback();
            $this->error('lab status not updated');
        }
    }
}
