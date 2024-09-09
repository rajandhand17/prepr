<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\PitchTemplate;
use App\Models\Project;
use App\Models\ProjectTemplate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectTemplateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-template-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project template data from legacy to learnlab';

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
            $this->info('Started Migration for project template data from legacy to learnlab');
            DB::beginTransaction();

            // Fetch all projects, including soft-deleted ones
            $projects = Project::withTrashed()->whereNotNull('challenge_id')->get();

            if ($projects->isNotEmpty()) {
                foreach ($projects as $project) {
                    // Fetch challenge pitch ID
                    $challengePitchId = DB::connection('mysql2')->table('challenge_pitches')->where('challenge_id', $project->challenge_id)->value('pitch_template_id');

                    // Fetch pitch template data based on challenge pitch ID
                    $pitchTemplateData = DB::connection('mysql2')->table('pitch_templates')->select('id', 'title')
                        ->when($challengePitchId !== '0', function ($query) use ($challengePitchId) {
                            return $query->where('id', $challengePitchId);
                        }, function ($query) {
                            return $query->whereNull('challenge_id');
                        })
                        ->first();

                    if ($pitchTemplateData) {
                        // Check for pitch template data in project_pitch_values or project_task_values
                        $pitchTemplate = DB::connection('mysql2')->table('project_pitch_values')->where(['pitch_template_id' => $pitchTemplateData->id, 'project_id' => $project->id])->first() ?? DB::connection('mysql2')->table('project_task_values')->where(['pitch_template_id' => $pitchTemplateData->id, 'project_id' => $project->id])->first();

                        if ($pitchTemplate && $pitchTemplate->pitch_template_id) {
                            // Check if the pitch template exists
                            $existingPitchTemplate = PitchTemplate::find($pitchTemplate->pitch_template_id);
                            if ($existingPitchTemplate) {
                                // Create and save new project template entry
                                ProjectTemplate::create([
                                    'project_id'  => $project->id,
                                    'template_id' => $existingPitchTemplate->id,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project template data');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
        }
    }
}
