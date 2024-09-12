<?php

namespace App\Console\Commands\ModuleProgressStatus;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Services\Manage\LabProgramService;
use App\Services\ModuleCompletionStatusService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LabProgramProgressStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-progress:lab-program-progress-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to check users progress in lab program';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Started to check users progress in lab program');
            DB::beginTransaction();
            $componentType = 'lab-program';
            $fetchComponentDataProgress = ModuleCompletionStatusService::fetchComponentDataProgress($componentType);
            if ($fetchComponentDataProgress->isNotEmpty()) {
                foreach ($fetchComponentDataProgress as $labProgramProgress) {
                    $fetchLabProgram = LabProgramService::getLabProgramBasedOnId($labProgramProgress->module_id);
                    if ($fetchLabProgram) {
                        TrackUserProgressHelper::trackLabProgramUserProgress($fetchLabProgram, $labProgramProgress->user_id);
                    }
                }
            }
            $this->info('Completed to check users progress in lab program');

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            DB::rollback();
            $this->error('lab program status not updated');
        }
    }
}
