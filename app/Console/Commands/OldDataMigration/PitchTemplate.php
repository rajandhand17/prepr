<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\PitchTemplate as PitchTemplates;
use DB;
use Illuminate\Console\Command;

class PitchTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:pitch-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old pitch template table data to new db structure.';

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
            $this->info('Migrating old data for pitch template table.');
            DB::beginTransaction();

            $pitch_templates = DB::connection('mysql2')->table('pitch_templates')->whereNull('deleted_at')->get();
            if ($pitch_templates->count() > 0) {
                foreach ($pitch_templates as $key => $single_pitch_templates) {
                    $check_pitch_templates = PitchTemplates::where('id', $single_pitch_templates->id)->first();
                    if ($check_pitch_templates) {
                        $newPitchTemplate = $check_pitch_templates;
                    } else {
                        $newPitchTemplate = new PitchTemplates();
                    }
                    $newPitchTemplate->id = $single_pitch_templates->id;
                    $newPitchTemplate->title = $single_pitch_templates->title;
                    $newPitchTemplate->save();
                }
                DB::commit();
                $this->info('Migrating of old data for pitch template table completed.');

                return;
            }
            DB::rollback();
            $this->error('No pitch template found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
