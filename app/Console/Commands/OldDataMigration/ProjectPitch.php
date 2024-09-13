<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePitch;
use App\Models\PitchTemplate;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectPitch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-pitchs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project pitch table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $insertArr = [];
            $this->info('Migrating old data for challenge pitch table.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('project_pitchs')->chunkById(1000, function ($project_pitchs) use ($insertArr) {
                foreach ($project_pitchs as $project_pitch) {
                    $pitchTemplateCheck = PitchTemplate::where('id', $project_pitch->pitch_template_id)->first();
                    if ($pitchTemplateCheck && $project_pitch->status == '1') {
                        $projectPitch = [
                            'template_id'           => $project_pitch->pitch_template_id,
                            'title'                 => $project_pitch->name,
                            'fr_CA_title'           => $project_pitch->name,
                            'description'           => $project_pitch->description,
                            'fr_CA_description'     => $project_pitch->description,
                            'created_at'            => Carbon::now(),
                            'updated_at'            => Carbon::now(),
                        ];
                        $check_pitch = ChallengePitch::find($project_pitch->id);
                        if (!$check_pitch) {
                            $insertArr[] = $projectPitch;
                        }
                    }
                }
                ChallengePitch::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for challenge pitch table completed.');

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
