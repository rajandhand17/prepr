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
    protected $signature = 'migrate-old-data:challenge-annoucements';

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

            $this->info('Migrating old data for Challenge annoucements table.');
            DB::beginTransaction();

            $challenge_announcements = DB::connection('mysql2')->table('challenge_announcement')->get();

            if($challenge_announcements->count() > 0){
                foreach ($challenge_announcements as $key => $single_challenge_announcements){
                    $challanges_annoucement_details=[
                        'user_id' => $single_challenge_announcements->user_id,
                        'challenge_id'=>$single_challenge_announcements->challenge_id, 
                        'custom_date_id'=>$single_challenge_announcements->customDateId, 
                        'sent_status'=>$single_challenge_announcements->sent_status, 
                        'title'=>$single_challenge_announcements->title, 
                        'body'=>$single_challenge_announcements->body, 
                        'schedule_status'=>$single_challenge_announcements->schedule_status, 
                        'announcement_number'=>$single_challenge_announcements->announcementNumber, 
                        'announcement_schedule'=>$single_challenge_announcements->announcementSchedule, 
                        'date'=>$single_challenge_announcements->date, 
                        'time'=>$single_challenge_announcements->time, 
                        'recipients'=>$single_challenge_announcements->recipients, 
                        'is_completed'=>$single_challenge_announcements->is_completed, 
                        'is_send'=>$single_challenge_announcements->is_send, 
                    ];
                    $check_category_annoucement = ChallengeAnnoucements::where($challanges_annoucement_details)->first();
                    if(!$check_category_annoucement){
                        ChallengeAnnoucements::create($challanges_annoucement_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for challanges annoucement table completed.');
                return;
            }
            DB::rollback();
            $this->error('No challanges annoucement found.');

        } catch (\Exception $e) {
           dd($e);
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
