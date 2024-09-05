<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserSetting as ModelsUserSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old user setting data to new database structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for user settings table started.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('users')->chunkById(1000, function ($users) {
                $userIds = $users->pluck('id')->unique()->toArray();
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();

                foreach ($users as $singleUser) {
                    if (!in_array($singleUser->id, $existingUsers)) {
                        continue;
                    }

                    // Retrieve or create the UserSetting instance
                    $userSetting = ModelsUserSetting::firstOrNew(['user_id' => $singleUser->id]);

                    // Map user settings to constants
                    $userSetting->fill([
                        'profile_privacy'                      => $this->mapPrivacy($singleUser->profile, 'user_privacy_options'),
                        'project_privacy'                      => $this->mapPrivacy($singleUser->project_privacy, 'project_privacy'),
                        'is_subscribe'                         => $this->mapSubscription($singleUser->is_subscribe),
                        'fcm_notification_permission'          => $this->mapNotificationPermission($singleUser->fcm_notification_permission),
                        'email_subscription_notification'      => $this->mapNotificationPermission($singleUser->email_subscription_notification),
                        'email_subscription_network_summary'   => $this->mapNotificationPermission($singleUser->email_subscription_network_summary),
                        'email_subscription_challenge_summary' => $this->mapNotificationPermission($singleUser->email_subscription_challenge_summary),
                        'email_subscription_lab_summary'       => $this->mapNotificationPermission($singleUser->email_subscription_lab_summary),
                        'display_lab_minionboarding'           => $this->mapNotificationPermission($singleUser->display_lab_minionboarding),
                        'display_challenge_minionboarding'     => $this->mapNotificationPermission($singleUser->display_challenge_minionboarding),
                        'display_org_minionboarding'           => $this->mapNotificationPermission($singleUser->display_org_minionboarding),
                        'manage_alerts'                        => $singleUser->manage_alerts,
                        'fcm_device_token'                     => $singleUser->fcm_device_token,
                    ]);

                    $userSetting->save();
                }
            });

            DB::commit();
            $this->info('Migrating old data for user settings table completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error: '.$e->getMessage());
        }
    }

    /**
     * Map profile or project privacy to constants.
     *
     * @param string $value
     * @param string $configKey
     *
     * @return mixed
     */
    private function mapPrivacy($value, $configKey)
    {
        return $value == 'public' ? config("constants.$configKey.public") : config("constants.$configKey.private");
    }

    /**
     * Map subscription status to constants.
     *
     * @param string $value
     *
     * @return mixed
     */
    private function mapSubscription($value)
    {
        return $value == 'subscribe' ? config('constants.subscribe_unsubscribe.subscribe') : config('constants.subscribe_unsubscribe.unsubscribe');
    }

    /**
     * Map notification permission value to constants.
     *
     * @param string $value
     *
     * @return mixed
     */
    private function mapNotificationPermission($value)
    {
        return $value == '0' ? config('constants.notification_permission.yes') : config('constants.notification_permission.no');
    }
}
