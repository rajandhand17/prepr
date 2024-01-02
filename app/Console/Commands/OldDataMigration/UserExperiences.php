<?php

namespace App\Console\Commands\OldDataMigration;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UserExperiences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-experiences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users experience';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users personal table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_experiences')->chunkById(1000, function ($userExperiences,$key) {
                foreach ($userExperiences as $experience) {
                    $checkUsers = \App\Models\User::find($experience->user_id);
                    if ($checkUsers == null) {
                        continue;
                    }
                    $checkUserExperiences=\App\Models\UserExperience::where("id",$experience->id)->first();
                    if($checkUserExperiences){
                        $userExperience=$checkUserExperiences;
                    }else{
                        $userExperience=new \App\Models\UserExperience();
                    }
                    $userExperience->user_id=$experience->user_id;
                    $userExperience->company=$experience->company;
                    $userExperience->position=$experience->position;
                    $userExperience->start_date=$experience->start_date;
                    $userExperience->end_date=$experience->end_date;
                    $userExperience->address=$experience->address;
                    $userExperience->state=$experience->state;
                    $userExperience->country=$experience->country;
                    $userExperience->description=$experience->description;
                    $userExperience->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users experience table completed.');
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
