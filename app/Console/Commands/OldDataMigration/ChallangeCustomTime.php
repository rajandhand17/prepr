<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ChallangeCustomTime as ChallangeCustomTimes;

class ChallangeCustomTime extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challange-custom-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old challange custom times table data to new db structure.';

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

            $this->info('Migrating old data for challange custom times table.');
            DB::beginTransaction();

            $challenge_custom_time = DB::connection('mysql2')->table('challenge_custom_time')->get();

            if($challenge_custom_time->count() > 0){
                foreach ($challenge_custom_time as $key => $single_challenge_custom_time){
                    $challenge_custom_time_details=[
                        'challenge_id' => $single_challenge_custom_time->challenge_id,
                        'title' => $single_challenge_custom_time->title,
                        'date' => $single_challenge_custom_time->date,
                        'description' => $single_challenge_custom_time->description,
                        'schedule_announcement' => $single_challenge_custom_time->scheduleAnnouncement,
                        'custom_date_number' => $single_challenge_custom_time->customDateNumber,
                        'custom_date_duration'=>$single_challenge_custom_time->customDateDuration,
                    ];
                    $check_challenge_custom_time = ChallangeCustomTimes::where($challenge_custom_time_details)->first();
                    if(!$check_challenge_custom_time){
                        ChallangeCustomTimes::create($challenge_custom_time_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for challenge custom time table completed.');
                return;
            }
            DB::rollback();
            $this->error('No challenge custom time found.');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
