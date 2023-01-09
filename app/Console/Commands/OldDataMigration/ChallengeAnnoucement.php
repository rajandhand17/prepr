<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ChallengeAnnouncement as ChallengeAnnoucements;

class ChallengeAnnoucement extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:Challenge-annoucements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old Challenge-annoucements table data to new db structure.';

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

            $this->info('Migrating old data for Challenge-annoucements table.');
            DB::beginTransaction();

            $challenge_announcements = DB::connection('mysql2')->table('challenge_announcements')->get();

            if($challenge_announcements->count() > 0){
                foreach ($challenge_announcements as $key => $single_challenge_announcements){
                    $challanges_details=[
                        'user_id' => $single_challenge_announcements->user_id,
                    ];
                    $check_category = ChallengeAnnoucements::where($challanges_details)->first();
                    if(!$check_category){
                        ChallengeAnnoucements::create($challanges_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for challanges table completed.');
                return;
            }
            DB::rollback();
            $this->error('No challanges found.');

        } catch (\Exception $e) {
        
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
