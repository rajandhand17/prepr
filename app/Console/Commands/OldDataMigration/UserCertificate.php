<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserCertificate as ModelUserCertificate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-certificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old data for users achievements.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users certificate table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_certificates')->chunkById(1000, function ($userCertificates) {
                $userIds = $userCertificates->pluck('user_id')->unique()->toArray();
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();

                foreach ($userCertificates as $singleUserCertificate) {
                    // Skip if the user does not exist
                    if (!in_array($singleUserCertificate->user_id, $existingUsers)) {
                        continue;
                    }

                    // Retrieve or create the UserCertificate
                    $userCertificate = ModelUserCertificate::updateOrCreate(
                        ['id' => $singleUserCertificate->id],
                        [
                            'user_id'     => $singleUserCertificate->user_id,
                            'company'     => $singleUserCertificate->company,
                            'name'        => $singleUserCertificate->name,
                            'start_date'  => $singleUserCertificate->start_date,
                            'end_date'    => $singleUserCertificate->end_date,
                            'description' => $singleUserCertificate->description,
                            'created_at'  => $this->parseDate($singleUserCertificate->created_at),
                            'updated_at'  => $this->parseDate($singleUserCertificate->updated_at),
                            'deleted_at'  => $this->parseDate($singleUserCertificate->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migration of old data for users certificate table completed.');
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
