<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChannelApiOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->firstOrCreate([
            'email' => 'magnet_sso@yopmail.com',
        ], [
            'username'       => 'magnet_sso_test',
            'first_name'     => 'Magnet',
            'last_name'      => 'SSO Test',
            'phone_number'   => '9876543214',
            'full_name'      => 'Magnet SSO Test',
            'magnet_user_id' => 1200,
            'password'       => Hash::make('Test@1234'),
            'verified_user' => '1'
        ]);
        Organization::query()->firstOrCreate([
            'slug' => 'magnet_test_organization',
        ], [
            'language'            => 'en',
            'uuid'                => Randomize::chars(10)->alphanumeric()->generate(),
            'user_id'             => $user->id,
            'title'               => 'Magnet Test Organization',
            'display_name'        => 'Magnet Test Organization',
            'description'         => 'Description',
            'is_verified'         => '1',
            'magnet_community_id' => 385,
            'total_employees'     => 10,
        ]);
    }
}
