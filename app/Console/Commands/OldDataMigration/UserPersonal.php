<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\UserPersonal as UserPersonals;

class UserPersonal extends Command
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
    protected $description = 'This command is use to migrate old user personal table data to new db structure.';

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
            $this->info('Migrating old data for user personals table.');
            DB::beginTransaction();

            $user_personals = DB::connection('mysql2')->table('user_personals')->get();
            if($user_personals->count() > 0){
                
                foreach ($user_personals as $key => $single_user_personals){
                   $user_personals_details=[
                        'user_id' => $single_user_personals->user_id,
                        'about'=>$single_user_personals->about,
                        'age'=>$single_user_personals->age,
                        'status'=>$single_user_personals->status,
                        'user_type'=>$single_user_personals->user_type,
                        'language'=>$single_user_personals->language,
                        'recent_immigrant'=>$single_user_personals->recent_immigrant,
                        'indigenous_group'=>$single_user_personals->indigenous_group,
                        'visible_minority'=>$single_user_personals->visible_minority,
                        'disability'=>$single_user_personals->disability,
                    ];
                    $check_user_personals = UserPersonals::where($user_personals_details)->first();
                    if(!$check_user_personals){
                        UserPersonals::create($user_personals_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for user personals table completed.');
                return;
            }
            DB::rollback();
            $this->error('No user personals found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
