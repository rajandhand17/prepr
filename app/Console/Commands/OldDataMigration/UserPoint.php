<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\UserPoint as UserPoints;

class UserPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:user-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old user-point table data to new db structure.';

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
            $this->info('Migrating old data for user-point table.');
            DB::beginTransaction();

            $user_points = DB::connection('mysql2')->table('user_points')->get();
            if($user_points->count() > 0){
                
                foreach ($user_points as $key => $single_user_points){
                   $user_points_details=[
                        'user_id' => $single_user_points->user_id,
                        'type' => $single_user_points->type,
                        'point'=>$single_user_points->point,
                        'date'=>$single_user_points->date,
                        'status'=>$single_user_points->status, 
                        'name'=>$single_user_points->name, 
                        'email'=>$single_user_points->email,  
                    ];
                    $check_user_points = UserPoints::where($user_points_details)->first();
                    if(!$check_user_points){
                        UserPoints::create($user_points_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for user points table completed.');
                return;
            }
            DB::rollback();
            $this->error('No user points found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
