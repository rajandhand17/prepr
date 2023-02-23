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
                'name' => 'Organization Owner',
                'display_name' => 'Organization Owner',
                'description'=>'Owner of organization, who invite orgnaization manager to manage organization'
            ],
            [
                'name' => 'Organization Manager',
                'display_name' => 'Organization Manager',
                'description'=>'Organization manager manage the organizations'
            ],[
                'name' => 'Lab Manager',
                'display_name' => 'Lab Manager',
                'description'=>'Lab Manager for lab managers'
            ],[
                'name' => 'Challenge Manager',
                'display_name' => 'Challenge Manager',
                'description'=>'Challenge Managers to '
            ],[
                'name' => 'Resource Manager',
                'display_name' => 'Resource Manager',
                'description'=>''
            ],[
                'name' => 'Assessor',
                'display_name' => 'Assessor',
                'description'=>''
            ],[
                'name' => 'Super Admin',
                'display_name' => 'Super Admin',
                'description'=>''
            ],[
                'name' => 'Customer Success',
                'display_name' => 'Customer Success',
                'description'=>''
            ],[
                'name' => 'Developer',
                'display_name' => 'Developer',
                'description'=>''
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
