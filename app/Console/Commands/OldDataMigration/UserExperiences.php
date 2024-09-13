<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
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
            DB::connection('mysql2')->table('user_experiences')->chunkById(1000, function ($userExperiences, $key) {
                foreach ($userExperiences as $experience) {
                    // Find or create the user
                    $user = \App\Models\User::findOrNew($experience->user_id);

                    // Skip if user not found
                    if (!$user->exists) {
                        continue;
                    }

                    // Find or create the user experience
                    $userExperience = \App\Models\UserExperience::findOrNew($experience->id);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($experience->created_at) ? Carbon::createFromTimestamp($experience->created_at) : null;
                    $updatedAt = !empty($experience->updated_at) ? Carbon::createFromTimestamp($experience->updated_at) : null;
                    $deletedAt = !empty($experience->deleted_at) ? Carbon::createFromTimestamp($experience->deleted_at) : null;
                    $start_date = !empty($experience->start_Date) ? Carbon::createFromTimestamp($experience->start_date) : null;                    // Fill the model attributes
                    $end_date = !empty($experience->end_Date) ? Carbon::createFromTimestamp($experience->end_Date) : null;                    // Fill the model attributes
                    if ($experience->company && $experience->position) {
                        $userExperience->fill([
                            'user_id'     => $experience->user_id,
                            'company'     => $experience->company,
                            'position'    => $experience->position,
                            'start_date'  => $start_date,
                            'end_date'    => $end_date,
                            'address'     => $experience->address,
                            'state'       => $experience->state,
                            'country'     => $experience->country,
                            'description' => $experience->description,
                            'created_at'  => $createdAt,
                            'updated_at'  => $updatedAt,
                            'deleted_at'  => $deletedAt,
                        ]);

                        // Save the model
                        $userExperience->save();
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users experience table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
