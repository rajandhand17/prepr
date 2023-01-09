<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ChallengePitche as ChallengePitches;

class ChallengePitche extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challenge-pitches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old challenge pitches table data to new db structure.';

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

            $this->info('Migrating old data for challenge pitches table.');
            DB::beginTransaction();

            $challenge_pitches = DB::connection('mysql2')->table('challenge_pitches')->get();
            if($challenge_pitches->count() > 0){
                
                foreach ($challenge_pitches as $key => $single_challenge_pitches){
                   $challenge_pitches_details=[
                        'challenge_id' => $single_challenge_pitches->challenge_id,
                        'pitch_template_id' => $single_challenge_pitches->pitch_template_id,
                    ];
                    $check_pitches_details = ChallengePitches::where($challenge_pitches_details)->first();
                    if(!$check_pitches_details){
                        ChallengePitches::create($challenge_pitches_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for check pitches table completed.');
                return; 
            }
            DB::rollback();
            $this->error('No check pitches found.');

        } catch (\Exception $e) {
           DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
