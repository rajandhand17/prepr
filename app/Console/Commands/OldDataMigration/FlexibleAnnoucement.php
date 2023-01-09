<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\FlexibleAnnouncement as FlexibleAnnouncements;

class FlexibleAnnoucement extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:flexible-announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old flexible annoucements table data to new db structure.';

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

            $this->info('Migrating old data for flexible annoucements table.');
            DB::beginTransaction();

            $flexible_announcements = DB::connection('mysql2')->table('flexible_announcement')->get();

            if($flexible_announcements->count() > 0){

                foreach ($flexible_announcements as $key => $single_flexible_announcements){
                    $flexible_announcements_details=[
                        'user_id' => $single_flexible_announcements->user_id,
                        'challenge_id' => $single_flexible_announcements->challenge_id,
                        'custom_date_id' => $single_flexible_announcements->customDateId,
                        'sent_status' => $single_flexible_announcements->sent_status,
                        'title' => $single_flexible_announcements->title,
                        'body' => $single_flexible_announcements->body,
                        'schedule_status' => $single_flexible_announcements->schedule_status,
                        'is_completed' => $single_flexible_announcements->is_completed,
                        'announcement_number' => $single_flexible_announcements->announcementNumber,
                        'announcement_schedule' => $single_flexible_announcements->announcementSchedule,
                    
                    ];
                    $check_flexible_announcements = FlexibleAnnouncements::where($flexible_announcements_details)->first();
                    if(!$check_flexible_announcements){
                        FlexibleAnnouncements::create($flexible_announcements_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for flexible annoucements table completed.');
                return;
            }
            DB::rollback();
            $this->error('No flexible annoucements found.');

        } catch (\Exception $e) {
            dd($e);
           DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
