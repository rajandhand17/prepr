<?php

namespace App\Console\Commands\OldDataMigration;

use DB;
use Illuminate\Console\Command;

class UserEducation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-education';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users education data to new database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users education table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_educations')->chunkById(1000, function ($userEducations) {
                foreach ($userEducations as $education) {
                    $checkUsers = \App\Models\User::find($education->user_id);
                    if ($checkUsers == null) {
                        continue;
                    }
                    $checkUserEducation = \App\Models\UserEducation::where('id', $education->id)->first();
                    if ($checkUserEducation) {
                        $userEducations = $checkUserEducation;
                    } else {
                        $userEducations = new \App\Models\UserEducation();
                    }
                    $userEducations->user_id = $education->user_id;
                    $userEducations->university = $education->university;
                    $userEducations->degree = $education->degree;
                    $userEducations->start_date = $education->start_date;
                    $userEducations->end_date = $education->end_date;
                    $userEducations->address = $education->address;
                    $userEducations->description = $education->description;
                    $userEducations->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users educations table completed.');
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
