<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'preferred_language'      => 'en',
                'preferred_timezone'      => 'EST',
                'first_name'              => 'Test',
                'last_name'               => 'Admin',
                'full_name'               => 'Test Prepr Admin',
                'username'                => 'TestPreprAdmin',
                'email'                   => 'testprepradmin@gmail.com',
                'email_verified_at'       => Carbon::now(),
                'password'                => Hash::make('Test@1234'),
                'country_code'            => '977',
                'phone_number'            => '9876543212',
                'two_factor_verification' => '0',
                'otp'                     => null,
                'profile_image'           => null,
                'referral_code'           => null,
                'remember_token'          => null,
                'created_at'              => Carbon::now(),
                'verified_user'           => '1',
                'is_profile_completed'    => '1',
            ],
            [
                'preferred_language'      => 'en',
                'preferred_timezone'      => 'EST',
                'first_name'              => 'Test Prepr',
                'last_name'               => 'Org Manager',
                'full_name'               => 'Test Prepr Org Manager',
                'username'                => 'testorgmanager',
                'email'                   => 'testorgmanager@gmail.com',
                'email_verified_at'       => Carbon::now(),
                'password'                => Hash::make('Test@1234'),
                'country_code'            => '977',
                'phone_number'            => '9876543213',
                'two_factor_verification' => '0',
                'otp'                     => null,
                'profile_image'           => null,
                'referral_code'           => null,
                'remember_token'          => null,
                'created_at'              => Carbon::now(),
                'verified_user'           => '1',
                'is_profile_completed'    => '1',
            ],
            [
                'preferred_language'      => 'en',
                'preferred_timezone'      => 'EST',
                'first_name'              => 'Test',
                'last_name'               => 'Users',
                'full_name'               => 'Test Users',
                'username'                => 'testusers',
                'email'                   => 'testuser@gmail.com',
                'email_verified_at'       => Carbon::now(),
                'password'                => Hash::make('Test@1234'),
                'country_code'            => '977',
                'phone_number'            => '9876543214',
                'two_factor_verification' => '0',
                'otp'                     => null,
                'profile_image'           => null,
                'referral_code'           => null,
                'remember_token'          => null,
                'created_at'              => Carbon::now(),
                'verified_user'           => '1',
                'is_profile_completed'    => '1',
            ],
            [
                'preferred_language'      => 'en',
                'preferred_timezone'      => 'EST',
                'first_name'              => 'Test',
                'last_name'               => 'Challenge Manager',
                'full_name'               => 'Test Challenge Manager',
                'username'                => 'testchallengemanager',
                'email'                   => 'testchallengemanager@gmail.com',
                'email_verified_at'       => Carbon::now(),
                'password'                => Hash::make('Test@1234'),
                'country_code'            => '977',
                'phone_number'            => '9876543215',
                'two_factor_verification' => '0',
                'otp'                     => null,
                'profile_image'           => null,
                'referral_code'           => null,
                'remember_token'          => null,
                'created_at'              => Carbon::now(),
                'verified_user'           => '1',
                'is_profile_completed'    => '1',
            ],
            [
                'preferred_language'      => 'en',
                'preferred_timezone'      => 'EST',
                'first_name'              => 'Test',
                'last_name'               => 'Lab Manager',
                'full_name'               => 'Test Lab Manager',
                'username'                => 'testlabmanager',
                'email'                   => 'testlabmanager@gmail.com',
                'email_verified_at'       => Carbon::now(),
                'password'                => Hash::make('Test@1234'),
                'country_code'            => '977',
                'phone_number'            => '9876543216',
                'two_factor_verification' => '0',
                'otp'                     => null,
                'profile_image'           => null,
                'referral_code'           => null,
                'remember_token'          => null,
                'created_at'              => Carbon::now(),
                'verified_user'           => '1',
                'is_profile_completed'    => '1',
            ],
        ];
        //create Super Admin
        foreach ($users as $user) {
            $createdUser = User::updateOrCreate([
                'username' => $user['username'],
            ], $user);
            $createdUser->attachRole('user');
        }
    }
}
