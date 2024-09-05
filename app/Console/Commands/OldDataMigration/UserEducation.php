<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserEducation as ModelUserEducation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Migrate old data for users education.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users education table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_educations')->chunkById(1000, function ($userEducations) {
                $userIds = $userEducations->pluck('user_id')->unique()->toArray();
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();

                foreach ($userEducations as $singleUserEducation) {
                    if (!in_array($singleUserEducation->user_id, $existingUsers)) {
                        continue;
                    }

                    $userEducation = ModelUserEducation::updateOrCreate(
                        ['id' => $singleUserEducation->id],
                        [
                            'user_id'      => $singleUserEducation->user_id,
                            'university'   => $singleUserEducation->university,
                            'degree'       => $singleUserEducation->degree,
                            'start_date'   => $this->parseDate($singleUserEducation->start_date),
                            'end_date'     => $this->parseDate($singleUserEducation->end_date),
                            'address'      => $singleUserEducation->address,
                            'description'  => $singleUserEducation->description,
                            'created_at'   => $this->parseDate($singleUserEducation->created_at),
                            'updated_at'   => $this->parseDate($singleUserEducation->updated_at),
                            'deleted_at'   => $this->parseDate($singleUserEducation->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migration of old data for users education table completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error: '.$e->getMessage());
        }
    }

    /**
     * Parse a timestamp or return null if empty.
     *
     * @param mixed $timestamp
     *
     * @return \Carbon\Carbon|null
     */
    private function parseDate($timestamp)
    {
        return !empty($timestamp) ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
