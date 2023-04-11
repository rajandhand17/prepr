<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\PitchTemplate as PitchTemplates;

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

            $pitch_templates = DB::connection('mysql2')->table('pitch_templates')->get();
            if($pitch_templates->count() > 0){

                foreach ($pitch_templates as $key => $single_pitch_templates){
                   $pitch_templates_details=[
                        'title' => $single_pitch_templates->title,
                        'challenge_id' => $single_pitch_templates->challenge_id,
                    ];
                    $check_pitch_templates = PitchTemplates::where($pitch_templates_details)->first();
                    if(!$check_pitch_templates){
                        PitchTemplates::create($pitch_templates_details);
                    }

                }
                DB::commit();
                $this->info('Migrating of old data for pitch template table completed.');
                return;
            }
            DB::rollback();
            $this->error('No pitch template found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
