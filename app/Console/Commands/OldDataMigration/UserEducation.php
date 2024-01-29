<?php

namespace App\Console\Commands\OldDataMigration;

use Carbon\Carbon;
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
    protected $description = 'This command will migrate all users educations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users educations table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_educations')->chunkById(1000, function ($userEducations, $key) {
                foreach ($userEducations as $single_user_education) {
                    // Check if the user exists
                    $checkUser = \App\Models\User::find($single_user_education->user_id);
                    if ($checkUser === null) {
                        continue;
                    }
                    // Retrieve an existing UserEducation or create a new one
                    $userEducation = \App\Models\UserEducation::firstOrNew(['id' => $single_user_education->id]);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($single_user_education->created_at) ? Carbon::parse($single_user_education->created_at) : null;
                    $updatedAt = !empty($single_user_education->updated_at) ? Carbon::parse($single_user_education->updated_at) : null;
                    $deletedAt = !empty($single_user_education->deleted_at) ? Carbon::parse($single_user_education->deleted_at) : null;

                    // Fill the model attributes
                    $userEducation->fill([
                        'user_id'      => $single_user_education->user_id,
                        'university'   => $single_user_education->university,
                        'degree'       => $single_user_education->degree,
                        'start_date'   => $single_user_education->start_date,
                        'end_date'     => $single_user_education->end_date,
                        'address'      => $single_user_education->address,
                        'description'  => $single_user_education->description,
                        'created_at'   => $createdAt,
                        'updated_at'   => $updatedAt,
                        'deleted_at'   => $deletedAt,
                    ]);
                    // Save the model
                    $userEducation->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users educations table completed.');
        }catch(\Exception $e){
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
