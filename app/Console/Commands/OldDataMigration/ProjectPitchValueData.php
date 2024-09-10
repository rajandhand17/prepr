<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePitch;
use App\Models\PitchTemplate;
use App\Models\Project;
use App\Models\ProjectPitchValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectPitchValueData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-pitch-value-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project pitch value data from legacy to learnlab';

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
            $this->info('Started Migration for project pitch value data from legacy to learnlab');
            DB::beginTransaction();

            // Fetch all projects, including soft-deleted ones
            $projects = Project::withTrashed()->get();

            if ($projects->isNotEmpty()) {
                foreach ($projects as $project) {
                    // For project pitch values
                    $projectPitchValues = DB::connection('mysql2')->table('project_pitch_values')->where(['project_id' => $project->id])->get();
                    if ($projectPitchValues->isNotEmpty()) {
                        foreach ($projectPitchValues as $pitchValue) {
                            $checkPitchTemplate = PitchTemplate::find($pitchValue->pitch_template_id);
                            if ($checkPitchTemplate) {
                                $getPitchData = ChallengePitch::find($pitchValue->pitch_id);
                                if ($getPitchData) {
                                    $createdAt = $pitchValue->created_at != null ? Carbon::createFromTimestamp($pitchValue->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $updatedAt = $pitchValue->updated_at != null ? Carbon::createFromTimestamp($pitchValue->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                    $deletedAt = $pitchValue->deleted_at != null ? Carbon::createFromTimestamp($pitchValue->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                    $newProjectPitchValue = new ProjectPitchValue();
                                    $newProjectPitchValue->id = $pitchValue->id;
                                    $newProjectPitchValue->project_id = $project->id;
                                    $newProjectPitchValue->pitch_template_id = $getPitchData->template_id;
                                    $newProjectPitchValue->project_pitch_id = $getPitchData->id;
                                    $newProjectPitchValue->description = $pitchValue->description ?? null;
                                    $newProjectPitchValue->created_at = $createdAt;
                                    $newProjectPitchValue->updated_at = $updatedAt;
                                    $newProjectPitchValue->deleted_at = $deletedAt;
                                    $newProjectPitchValue->save();
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project pitch value data');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
        }
    }
}
