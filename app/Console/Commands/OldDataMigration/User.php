<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old tag table data to new db structure.';

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
            $this->info('Migrating old data for users table.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('users')->chunkById(1000, function ($users) {
                foreach ($users as $key => $single_user) {
                    if ($single_user->email == null) {
                        continue;
                    }

                    $language = 'en';
                    $twofactor = '0';
                    $verified = '0';

                    if ($single_user->language_id == '1' || $single_user->language_id == '0') {
                        $language = 'en';
                    } elseif ($single_user->language_id == '2') {
                        $language = 'fr-CA';
                    }
                    if ($single_user->two_factor == 'allow') {
                        $twofactor = '1';
                    }

                    if ($single_user->is_verify == '1') {
                        $verified = '1';
                    }

                    if ($single_user->username != null && !empty($single_user->username)) {
                        $username = $username_format = $single_user->username;
                        $next = 1;
                        while (\App\Models\User::where('username', '=', $username)->first()) {
                            $username = "{$username_format}{$next}";
                            $next++;
                        }
                    } else {
                        $username = null;
                    }

                    $checkUser = \App\Models\User::where('email', $single_user->email)->first();

                    if ($checkUser) {
                        $user = $checkUser;
                    } else {
                        $user = new \App\Models\User();
                    }

                    $user->id = $single_user->id;
                    $user->preferred_language = $language;
                    $user->first_name = $single_user->first_name;
                    $user->last_name = $single_user->last_name;
                    $user->full_name = $single_user->name;
                    $user->username = $username;
                    $user->email = $single_user->email;
                    $user->email_verified_at = Carbon::createFromTimestamp($single_user->created_at);
                    $user->password = $single_user->password;
                    $user->country_code = $single_user->country_code;
                    $user->phone_number = $single_user->phone_number;
                    $user->two_factor_verification = $twofactor;
                    $user->otp = $single_user->two_factor_otp;
                    $user->profile_image = (!empty($single_user->profile_image)) ? $single_user->profile_image : config('site-settings.default_user_profile_image');
                    $user->referral_code = $single_user->referal_code;
                    $user->user_points = $single_user->point;
                    $user->user_rank = $single_user->rank;
                    $user->achievement_count = $single_user->achievement_count;
                    $user->remember_token = $single_user->remember_token;
                    $user->created_at = Carbon::createFromTimestamp($single_user->created_at);
                    $user->verified_user = $verified;
                    $user->is_profile_completed = '1';
                    $user->save();

                    $user->attachRole('user');
                }
            });

            DB::commit();
            $this->info('Migrating of old data for users table completed.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
