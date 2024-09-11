<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserPersonal as ModelUserPersonal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserPersonal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-personal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old data for users personal data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users personal table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_personals')->chunkById(1000, function ($userPersonals) {
                $userIds = $userPersonals->pluck('user_id')->unique()->toArray();
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();

                foreach ($userPersonals as $userPersonalDetail) {
                    if (!in_array($userPersonalDetail->user_id, $existingUsers)) {
                        continue;
                    }

                    $userPersonal = ModelUserPersonal::findOrNew($userPersonalDetail->id);

                    $userPersonal->fill([
                        'user_id'          => $userPersonalDetail->user_id,
                        'about'            => $userPersonalDetail->about ?? null,
                        'gender'           => $this->mapGender($userPersonalDetail->gender),
                        'date_of_birth'    => $userPersonalDetail->date_of_birth ?? null,
                        'age'              => $userPersonalDetail->age,
                        'purpose'          => $this->mapStatus($userPersonalDetail->status),
                        'user_type'        => $this->mapUserType($userPersonalDetail->user_type),
                        'recent_immigrant' => $this->mapBoolean($userPersonalDetail->recent_immigrant),
                        'indigenous_group' => $this->mapBoolean($userPersonalDetail->indigenous_group),
                        'visible_minority' => $this->mapBoolean($userPersonalDetail->visible_minority),
                        'disability'       => $this->mapBoolean($userPersonalDetail->disability),
                    ]);

                    $userPersonal->save();
                }
            });

            DB::commit();
            $this->info('Migration of old data for users personal table completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error: '.$e->getMessage());
        }
    }

    /**
     * Map status to a standardized value.
     *
     * @param string $status
     *
     * @return string
     */
    private function mapStatus($status)
    {
        $statusMap = [
            'looking_team'            => '0',
            'currently_mentor'        => '1',
            'looking_employers'       => '2',
            'looking_to_build_skills' => '12',
            'default'                 => '1',
        ];

        return $statusMap[$status] ?? $statusMap['default'];
    }

    /**
     * Map user type to a standardized value.
     *
     * @param string $userType
     *
     * @return string|null
     */
    private function mapUserType($userType)
    {
        $userTypeMap = [
            'employee'                => '0',
            'investor'                => '1',
            'community_organization'  => '23',
            'default'                 => null,
        ];

        return $userTypeMap[$userType] ?? $userTypeMap['default'];
    }

    /**
     * Map gender to a standardized value.
     *
     * @param string $gender
     *
     * @return string
     */
    private function mapGender($gender)
    {
        $genderMap = [
            'male'    => '0',
            'female'  => '1',
            'other'   => '2',
            'decline' => '3',
            'default' => '3',
        ];

        return $genderMap[$gender] ?? $genderMap['default'];
    }

    /**
     * Map a boolean-like field to a standardized value.
     *
     * @param mixed $value
     *
     * @return string
     */
    private function mapBoolean($value)
    {
        return $value == '1' ? '2' : '1';
    }
}
