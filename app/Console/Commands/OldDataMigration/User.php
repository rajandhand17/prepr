<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User as UserModel;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UserMigration extends Command
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
    protected $description = 'This command is used to migrate old user data to the new DB structure.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users table...');
            DB::beginTransaction();

            DB::connection('mysql2')->table('users')->chunkById(1000, function ($users) {
                $insertData = [];

                foreach ($users as $single_user) {
                    if (is_null($single_user->email)) {
                        continue;
                    }

                    // Map old values to new user model
                    $userData = $this->mapUserData($single_user);

                    // Check if user already exists and update or create accordingly
                    $user = UserModel::updateOrCreate(['email' => $single_user->email], $userData);

                    // Assign 'user' role if newly created
                    if (!$user->wasRecentlyCreated) {
                        $user->attachRole('user');
                    }
                }
            });

            DB::commit();
            $this->info('Migrating of old data for users table completed.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    /**
     * Map old user data to new user structure.
     *
     * @param object $single_user
     *
     * @return array
     */
    private function mapUserData($single_user)
    {
        $language = $this->mapLanguage($single_user->language_id);
        $twoFactor = $single_user->two_factor === 'allow' ? '1' : '0';
        $verified = $single_user->is_verify === '1' ? '1' : '0';

        $username = $this->generateUniqueUsername($single_user->username);

        return [
            'id'                                   => $single_user->id,
            'preferred_language'                   => $language,
            'first_name'                           => $single_user->first_name,
            'last_name'                            => $single_user->last_name,
            'full_name'                            => $single_user->name,
            'username'                             => $username,
            'email'                                => $single_user->email,
            'email_verified_at'                    => Carbon::createFromTimestamp($single_user->created_at),
            'password'                             => $single_user->password,
            'country_code'                         => $single_user->country_code,
            'phone_number'                         => $single_user->phone_number,
            'two_factor_verification'              => $twoFactor,
            'otp'                                  => $single_user->two_factor_otp,
            'profile_image'                        => $single_user->profile_image ?? config('site-settings.default_user_profile_image'),
            'referral_code'                        => $single_user->referal_code,
            'user_points'                          => $single_user->point,
            'user_rank'                            => $single_user->rank,
            'achievement_count'                    => $single_user->achievement_count,
            'remember_token'                       => $single_user->remember_token,
            'display_lab_mini_onboarding'          => $single_user->display_lab_minionboarding == '1' ? '0' : '1',
            'display_challenge_mini_onboarding'    => $single_user->display_challenge_minionboarding == '1' ? '0' : '1',
            'display_organization_mini_onboarding' => $single_user->display_org_minionboarding == '1' ? '0' : '1',
            'created_at'                           => Carbon::createFromTimestamp($single_user->created_at),
            'verified_user'                        => $verified,
            'is_profile_completed'                 => $single_user->is_profile_completed === 'no' ? '0' : '1',
            'go1_id'                               => $single_user->go1_id,
            'go1_user_metadata'                    => $single_user->go1_user_metadata,
            'magnet_user_id'                       => $single_user->magnet_user_id,
            'magnet_user_role'                     => $single_user->magnet_user_role,
        ];
    }

    /**
     * Map language ID to language code.
     *
     * @param int $languageId
     *
     * @return string
     */
    private function mapLanguage($languageId)
    {
        return match ($languageId) {
            1, 0 => 'en',
            2       => 'fr-CA',
            default => 'en',
        };
    }

    /**
     * Generate a unique username if needed.
     *
     * @param string|null $username
     *
     * @return string|null
     */
    private function generateUniqueUsername($username)
    {
        if ($username) {
            $baseUsername = $username;
            $counter = 1;

            while (UserModel::where('username', '=', $username)->exists()) {
                $username = "{$baseUsername}{$counter}";
                $counter++;
            }

            return $username;
        }

        return null;
    }
}
