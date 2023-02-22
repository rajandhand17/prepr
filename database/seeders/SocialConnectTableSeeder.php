<?php

namespace Database\Seeders;

use App\Models\SocialConnect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class SocialConnectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $social_connect_list = [
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
                'description'=>''
            ],[
                'name' => 'Challenge Manager',
                'display_name' => 'Challenge Manager',
                'description'=>''
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

        foreach ($social_connect_list as $key => $single_social_conect){
            Role::updateOrCreate(
                ['name' =>  $single_social_conect['name']],
                ['display_name' => $single_social_conect['display_name']],
                ['description' => $single_social_conect['description']],
            );
        }
    }
}
