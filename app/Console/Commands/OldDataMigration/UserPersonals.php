<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\UserPersonal;

class UserPersonals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:user-personal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old host table data to new db structure.';

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

            $this->info('Migrating old data for user personal table.');
            DB::beginTransaction();

            $user_personals = DB::connection('mysql2')->table('user_personals')->get();
            if($user_personals->count() > 0){
                
                foreach ($user_personals as $key => $single_user_personal){
                   $users_personals_details=[
                        'user_id' => $single_user_personal->user_id,
                        'about' => $single_user_personal->about,
                        'gender' => $single_user_personal->gender,
                        'date_of_birth' => $single_user_personal->date_of_birth,
                        'age' => $single_user_personal->age,
                        'status' => $single_user_personal->status,
                        'user_type' => $single_user_personal->user_type,
                        'language' => $single_user_personal->language,
                        'recent_immigrant' => $single_user_personal->recent_immigrant,
                        'indigenous_group' => $single_user_personal->indigenous_group,
                        'visible_minority' => $single_user_personal->visible_minority,
                        'disability' => $single_user_personal->disability,
                    ];
                    $check_users_personal = UserPersonal::where($users_personals_details)->first();
                    if(!$check_users_personal){
                        UserPersonal::create($users_personals_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for users personal table completed.');
                return;
            }
            DB::rollback();
            $this->error('No users personal found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
