<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
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
                'name' => 'organization_owner',
                'display_name' => 'Organization Owner',
                'description'=>'Owner of organization, who invite orgnaization manager to manage organization'
            ],
            [
                'name' => 'organization_manager',
                'display_name' => 'Organization Manager',
                'description'=>'Organization manager manage the organizations'
            ],[
                'name' => 'lab_manager',
                'display_name' => 'Lab Manager',
                'description'=>'Lab Manager for lab managers'
            ],[
                'name' => 'challenge_manager',
                'display_name' => 'Challenge Manager',
                'description'=>'Challenge managers manage to challenge'
            ],[
                'name' => 'resource_manager',
                'display_name' => 'Resource Manager',
                'description'=>'Resource manager manage the resurces'
            ],[
                'name' => 'user',
                'display_name' => 'User',
                'description'=>'User manage for users'
            ],[
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description'=>'Who manage everything'
            ],[
                'name' => 'customer_success',
                'display_name' => 'Customer Success',
                'description'=>'Manage the customer success'
            ],[
                'name' => 'developer',
                'display_name' => 'Developer',
                'description'=>'Manage the developers work'
            ]
            ];

        foreach ($roles as $key => $roles_conect){
            Role::updateOrCreate(
                ['name' =>  $roles_conect['name']],
                ['display_name' => $roles_conect['display_name']],
                ['description' => $roles_conect['description']],
            );
        }
    }
}
