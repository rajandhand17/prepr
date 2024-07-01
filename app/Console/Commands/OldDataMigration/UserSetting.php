<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserSetting as ModelsUserSetting;
use DB;
use Illuminate\Console\Command;

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
    protected $description = 'This command is use to migrate old user setting data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $insertArr = [];
            $this->info('Migrating of old data for user setting table started.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('users')->chunkById(1000, function ($users) {
                foreach ($users as $key => $single_user) {
                    $existsUsers = User::where('id', $single_user->id)->first();
                    if (!$existsUsers) {
                        continue;
                    }
                    $existInUserSettings = ModelsUserSetting::where('user_id', $single_user->id)->first();
                    if ($existInUserSettings) {
                        continue;
                    }
                    if ($single_user->profile == 'public') {
                        $profile_privacy = config('constants.user_privacy_options.public');
                    } else {
                        $profile_privacy = config('constants.user_privacy_options.private');
                    }
                    if ($single_user->project_privacy == 'private') {
                        $project_privacy = config('constants.project_privacy.private');
                    } else {
                        $project_privacy = config('constants.project_privacy.public');
                    }
                    if ($single_user->is_subscribe == 'subscribe') {
                        $is_subscribe = config('constants.subscribe_unsubscribe.subscribe');
                    } else {
                        $is_subscribe = config('constants.subscribe_unsubscribe.unsubscribe');
                    }
                    if ($single_user->fcm_notification_permission == '0') {
                        $fcm_notification_permission = config('constants.notification_permission.yes');
                    } else {
                        $fcm_notification_permission = config('constants.notification_permission.no');
                    }
                    if ($single_user->email_subscription_network_summary == '0') {
                        $email_subscription_network_summary = config('constants.notification_permission.yes');
                    } else {
                        $email_subscription_network_summary = config('constants.notification_permission.no');
                    }
                    if ($single_user->email_subscription_challenge_summary == '0') {
                        $email_subscription_challenge_summary = config('constants.notification_permission.yes');
                    } else {
                        $email_subscription_challenge_summary = config('constants.notification_permission.no');
                    }
                    if ($single_user->email_subscription_notification == '0') {
                        $email_subscription_notification = config('constants.notification_permission.yes');
                    } else {
                        $email_subscription_notification = config('constants.notification_permission.no');
                    }
                    if ($single_user->email_subscription_lab_summary == '0') {
                        $email_subscription_lab_summary = config('constants.notification_permission.yes');
                    } else {
                        $email_subscription_lab_summary = config('constants.notification_permission.no');
                    }

                    if ($single_user->display_lab_minionboarding == '0') {
                        $display_lab_minionboarding = config('constants.notification_permission.yes');
                    } else {
                        $display_lab_minionboarding = config('constants.notification_permission.no');
                    }
                    if ($single_user->display_challenge_minionboarding == '0') {
                        $display_challenge_minionboarding = config('constants.notification_permission.yes');
                    } else {
                        $display_challenge_minionboarding = config('constants.notification_permission.no');
                    }
                    if ($single_user->display_org_minionboarding == '0') {
                        $display_org_minionboarding = config('constants.notification_permission.yes');
                    } else {
                        $display_org_minionboarding = config('constants.notification_permission.no');
                    }
                    $checkUserSetting = ModelsUserSetting::where('user_id', $single_user->id)->first();
                    if ($checkUserSetting) {
                        $userSetting = $checkUserSetting;
                    } else {
                        $userSetting = new ModelsUserSetting();
                    }
                    $userSetting->user_id = $single_user->id;
                    $userSetting->profile_privacy = $profile_privacy;
                    $userSetting->project_privacy = $project_privacy;
                    $userSetting->manage_alerts = $single_user->manage_alerts;
                    $userSetting->is_subscribe = $is_subscribe;
                    $userSetting->email_subscription_notification = $email_subscription_notification;
                    $userSetting->email_subscription_network_summary = $email_subscription_network_summary;
                    $userSetting->email_subscription_challenge_summary = $email_subscription_challenge_summary;
                    $userSetting->email_subscription_lab_summary = $email_subscription_lab_summary;
                    $userSetting->display_lab_minionboarding = $display_lab_minionboarding;
                    $userSetting->display_challenge_minionboarding = $display_challenge_minionboarding;
                    $userSetting->display_org_minionboarding = $display_org_minionboarding;
                    $userSetting->fcm_notification_permission = $fcm_notification_permission;
                    $userSetting->fcm_device_token = $single_user->fcm_device_token;
                    $userSetting->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for user setting table completed.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
