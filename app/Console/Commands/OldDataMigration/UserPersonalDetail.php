<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\UserPersonal;
use Illuminate\Console\Command;
use DB;
use PhpParser\Builder\Class_;

Class UserPersonalDetail extends Command
{  
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:user_personal';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old tag table data to new db structure.';

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
            $this->info('Migrating old data for users personal detail table.');
            DB::beginTransaction();
            $userpersonaldetail = DB::connection('mysql2')->table('user_personals')->get();
            if($userpersonaldetail->count() > 0){
                foreach ($userpersonaldetail as $key => $single_user){
                   $users_details=[
                        'user_id'=>$single_user->user_id,    
                        'about' => $single_user->about,
                        'gender' => $single_user->gender,
                        'date_of_birth' => $single_user->date_of_birth,
                        'age' => $single_user->age,
                        'purpose' => $single_user->purpose,
                        'user_type'=>$single_user->user_type,
                        'recent_immigrant'=>$single_user->recent_immigrant,
                        'indigenous_group'=>$single_user->indigenous_group,
                        'visible_minority'=>$single_user->visible_minority,
                        'disability'=>$single_user->disability,
                    ];
                    $check_users = UserPersonal::where($users_details)->first();
                    if(!$check_users){
                        $userPersonal=UserPersonal::create($users_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for users personal detail table completed.');
                return;
            }
            DB::rollback();
            $this->error('No user personal detail found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }


}