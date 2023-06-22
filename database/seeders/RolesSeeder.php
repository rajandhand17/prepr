<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name'         => 'organization_owner',
                'display_name' => 'Organization Owner',
                'description'  => 'Owner of organization, who invite orgnaization manager to manage organization',
                'role_type'    => '1',
            ],
            [
                'name'         => 'organization_manager',
                'display_name' => 'Organization Manager',
                'description'  => 'Organization manager manage the organizations',
                'role_type'    => '1',
            ], [
                'name'         => 'lab_manager',
                'display_name' => 'Lab Manager',
                'description'  => 'Lab Manager for lab managers',
                'role_type'    => '1',
            ], [
                'name'         => 'challenge_manager',
                'display_name' => 'Challenge Manager',
                'description'  => 'Challenge managers manage to challenge',
                'role_type'    => '1',
            ], [
                'name'         => 'resource_manager',
                'display_name' => 'Resource Manager',
                'description'  => 'Resource manager manage the resurces',
                'role_type'    => '1',
            ], [
                'name'         => 'user',
                'display_name' => 'User',
                'description'  => 'User manage for users',
                'role_type'    => '1',
            ], [
                'name'         => 'super_admin',
                'display_name' => 'Super Admin',
                'description'  => 'Who manage everything',
                'role_type'    => '0',
            ], [
                'name'         => 'customer_success',
                'display_name' => 'Customer Success',
                'description'  => 'Manage the customer success',
                'role_type'    => '0',
            ], [
                'name'         => 'developer',
                'display_name' => 'Developer',
                'description'  => 'Manage the developers work',
                'role_type'    => '0',
            ],
        ];

        foreach ($roles as $key => $role) {
            Role::updateOrCreate(
                [
                    'name'         => $role['name'],
                    'display_name' => $role['display_name'],
                ],
                [
                    'description'  => $role['description'],
                    'role_type'    => $role['role_type'],
                ]
            );
        }
    }
}
