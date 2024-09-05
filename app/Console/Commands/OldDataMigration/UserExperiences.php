<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserExperience;

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
    protected $description = 'Migrate old data for users experiences.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users experiences table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_experiences')->chunkById(1000, function ($userExperiences) {
                $userIds = $userExperiences->pluck('user_id')->unique()->toArray();
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();

                foreach ($userExperiences as $experience) {
                    if (!in_array($experience->user_id, $existingUsers)) {
                        continue;
                    }

                    // Find or create the user experience
                    $userExperience = UserExperience::updateOrCreate(
                        ['id' => $experience->id],
                        [
                            'user_id'     => $experience->user_id,
                            'company'     => $experience->company,
                            'position'    => $experience->position,
                            'start_date'  => $this->parseDate($experience->start_date),
                            'end_date'    => $this->parseDate($experience->end_date),
                            'address'     => $experience->address,
                            'state'       => $experience->state,
                            'country'     => $experience->country,
                            'description' => $experience->description,
                            'created_at'  => $this->parseDate($experience->created_at),
                            'updated_at'  => $this->parseDate($experience->updated_at),
                            'deleted_at'  => $this->parseDate($experience->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migration of old data for users experiences table completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Parse a timestamp or return null if empty.
     *
     * @param  mixed $timestamp
     * @return \Carbon\Carbon|null
     */
    private function parseDate($timestamp)
    {
        return !empty($timestamp) ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
