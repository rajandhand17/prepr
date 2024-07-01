<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTask;
use App\Models\PitchTemplate;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project task table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $insertArr = [];
            $this->info('Migrating old data for challenge task table.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('project_tasks')->chunkById(1000, function ($project_tasks) use ($insertArr) {
                foreach ($project_tasks as $project_task) {
                    $pitchTemplateCheck = PitchTemplate::where('id', $project_task->pitch_template_id)->first();
                    if ($pitchTemplateCheck) {
                        $projectTask = [
                            'template_id'   => $project_task->pitch_template_id,
                            'title'         => $project_task->name,
                            'fr_CA_title'   => $project_task->name,
                            'created_at'    => Carbon::now(),
                            'updated_at'    => Carbon::now(),
                        ];
                        $check_task = ChallengeTask::find($project_task->id);
                        if (!$check_task) {
                            $insertArr[] = $projectTask;
                        }
                    }
                }
                ChallengeTask::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for challenge task table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
