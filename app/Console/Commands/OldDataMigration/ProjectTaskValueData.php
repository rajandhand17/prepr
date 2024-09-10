<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTask;
use App\Models\PitchTemplate;
use App\Models\Project;
use App\Models\ProjectTaskValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectTaskValueData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-task-value-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project task value data from legacy to learnlab';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Started Migration for project task value data from legacy to learnlab');
            DB::beginTransaction();

            // Fetch all projects, including soft-deleted ones
            $projects = Project::withTrashed()->get();

            if ($projects->isNotEmpty()) {
                foreach ($projects as $project) {
                    // For project task values
                    $projectTaskValues = DB::connection('mysql2')->table('project_task_values')->where(['project_id' => $project->id])->get();
                    if ($projectTaskValues->isNotEmpty()) {
                        foreach ($projectTaskValues as $taskValue) {
                            $checkPitchTemplate = PitchTemplate::find($taskValue->pitch_template_id);
                            if ($checkPitchTemplate) {
                                $getTaskData = ChallengeTask::find($taskValue->project_task_id);
                                if ($getTaskData) {
                                    $completedAt = $taskValue->complete_datetime != null ? Carbon::createFromTimestamp($taskValue->complete_datetime)->translatedFormat('Y-m-d H:i:s') : null;
                                    $createdAt = $taskValue->created_at != null ? Carbon::createFromTimestamp($taskValue->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $updatedAt = $taskValue->updated_at != null ? Carbon::createFromTimestamp($taskValue->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $deletedAt = $taskValue->deleted_at != null ? Carbon::createFromTimestamp($taskValue->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                    $newProjectTaskValue = new ProjectTaskValue();
                                    $newProjectTaskValue->id = $taskValue->id;
                                    $newProjectTaskValue->project_id = $project->id;
                                    $newProjectTaskValue->task_template_id = $getTaskData->template_id;
                                    $newProjectTaskValue->project_task_id = $getTaskData->id;
                                    $newProjectTaskValue->status = $taskValue->is_completed == '1' ? '1' : '0';
                                    $newProjectTaskValue->completed_date = $completedAt;
                                    $newProjectTaskValue->created_at = $createdAt;
                                    $newProjectTaskValue->updated_at = $updatedAt;
                                    $newProjectTaskValue->deleted_at = $deletedAt;
                                    $newProjectTaskValue->save();
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project task value data');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
        }
    }
}
