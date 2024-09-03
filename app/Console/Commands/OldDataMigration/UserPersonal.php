<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use DB;
use Illuminate\Console\Command;

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
    protected $description = 'This command will migrate all users personal data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users personal table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_personals')->chunkById(1000, function ($userPersonals, $key) {
                foreach ($userPersonals as $key => $userPersonalDetail) {
                    $checkUsers = \App\Models\User::find($userPersonalDetail->user_id);

                    if ($checkUsers === null) {
                        continue;
                    }

                    $userPersonal = \App\Models\UserPersonal::findOrNew($userPersonalDetail->id);

                    // Map status
                    $statusMap = [
                        'looking_team'      => '0',
                        'currently_mentor'  => '1',
                        'looking_employers' => '2',
                        // ... (add other cases)
                        'looking_to_build_skills' => '12',
                        // Default
                        'default' => '1',
                    ];
                    $status = $statusMap[$userPersonalDetail->status] ?? $statusMap['default'];

                    // Map user_type
                    $userTypeMap = [
                        'employee' => '0',
                        'investor' => '1',
                        // ... (add other cases)
                        'community_organization' => '23',
                        // Default
                        'default' => null,
                    ];
                    $user_type = $userTypeMap[$userPersonalDetail->user_type] ?? $userTypeMap['default'];

                    // Map gender
                    $genderMap = [
                        'male'    => '0',
                        'female'  => '1',
                        'other'   => '2',
                        'decline' => '3',
                        // Default
                        'default' => '3',
                    ];
                    $gender = $genderMap[$userPersonal->gender] ?? $genderMap['default'];

                    $userPersonal->user_id = $userPersonalDetail->user_id;
                    $userPersonal->about = $userPersonalDetail->about ?? null;
                    $userPersonal->gender = $gender;
                    $userPersonal->date_of_birth = $userPersonalDetail->date_of_birth ?? null;
                    $userPersonal->age = $userPersonalDetail->age;
                    $userPersonal->purpose = $status;
                    $userPersonal->user_type = $user_type;
                    $userPersonal->recent_immigrant = $userPersonalDetail->recent_immigrant == '1' ? '2' : '1';
                    $userPersonal->indigenous_group = $userPersonalDetail->indigenous_group == '1' ? '2' : '1';
                    $userPersonal->visible_minority = $userPersonalDetail->visible_minority == '1' ? '2' : '1';
                    $userPersonal->disability = $userPersonalDetail->disability == '1' ? '2' : '1';
                    $userPersonal->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users personal table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
