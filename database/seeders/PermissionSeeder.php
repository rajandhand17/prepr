<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission=[
            [
                'name' => 'view_organization',
                'display_name' => 'Organization View',
                'description'=>'Organization view permission to who can view organization'
            ],[
                'name' => 'create_organization',
                'display_name' => 'Create Organization',
                'description'=>'Organization create permission to who can create organization'
            ],
            [
                'name' => 'edit_organization',
                'display_name' => 'Edit Organization',
                'description'=>'Organization edit permission to who can edit organization'
            ],
            [
                'name' => 'delete_organization',
                'display_name' => 'Delete Organization',
                'description'=>'Organization delete permission to who can delete organization'
            ],
            [
                'name' => 'view_organization_member_management',
                'display_name' => 'View Organization Member Manager',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'create_organization_member_management',
                'display_name' => 'Create Organization Member Manager',
                'description'=>'Organization member manager manage the organizations'
            ],
            [
                'name' => 'edit_organization_member_management',
                'display_name' => 'Edit Organization Member Manager',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'delete_organization_member_management',
                'display_name' => 'Delete Organization Member Manager',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'view_lab',
                'display_name' => 'View Lab',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'create_lab',
                'display_name' => 'Create Lab',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'edit_lab',
                'display_name' => 'Edit Lab',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'delete_lab',
                'display_name' => 'Delete Lab',
                'description'=>'Organization manager manage the organizations'
            ], 
            [
                'name' => 'generate_report_lab',
                'display_name' => 'Generate Report Lab',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'view_lab_member_management',
                'display_name' => 'View Lab Member Management',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'create_lab_member_management',
                'display_name' => 'Create Lab Member Management',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'edit_lab_member_management',
                'display_name' => 'Edit Lab Member Management',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'delete_lab_member_management',
                'display_name' => 'Delete Lab Memeber Management',
                'description'=>'Organization manager manage the organizations'
            ], [
                'name' => 'view_lab_programs',
                'display_name' => 'View Lab Programs',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'create_lab_programs',
                'display_name' => 'Create Lab Programs',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'edit_lab_programs',
                'display_name' => 'Edit Lab Programs',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'delete_lab_programs',
                'display_name' => 'Delete Lab programs',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'view_challenge',
                'display_name' => 'View Challenge',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'create_challenge',
                'display_name' => 'Create Challenge',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'edit_challenge',
                'display_name' => 'Edit Challenge',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'delete_challenge',
                'display_name' => 'Delete Challenge',
                'description'=>'Organization manager manage the organizations'
            ], 
            [
                'name' => 'generate_report_challenge',
                'display_name' => 'Generate Report Challenge',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'clone_challenge',
                'display_name' => 'Clone Challenge',
                'description'=>'Organization manager manage the organizations'
            ],
            
            [
                'name' => 'create_challenge_annoucements',
                'display_name' => 'Create Challenge Annoucements',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'edit_challenge_annoucements',
                'display_name' => 'Edit Challenge Annoucements',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'delete_challenge_annoucements',
                'display_name' => 'Delete Challenge Annoucements',
                'description'=>'Organization manager manage the organizations'
            ], 
            [
                'name' => 'create_challenge_assessment',
                'display_name' => 'Create Challenge Assessment',
                'description'=>'Organization manager manage the organizations'
            ],
            [
                'name' => 'view_project_submission',
                'display_name' => 'View Project Submission',
                'description'=>'View Project Submission',
            ], 
            [
                'name' => 'remove_project_submission',
                'display_name' => 'Remove Project Submission',
                'description'=>'Remove Project Submission',
            ], 
            [
                'name' => 'view_challenges_members_management',
                'display_name' => 'View Challenges Members Management',
                'description'=>'View Challenges Members Management',
            ], 
            
            [
                'name' => 'create_challenges_members_management',
                'display_name' => 'Create Challenges Members Management',
                'description'=>'Create Challenges Members Management',
            ], 
            [
                'name' => 'edit_challenges_members_management',
                'display_name' => 'Edit Challenges Members Management',
                'description'=>'Edit Challenges Members Management',
            ], 
            [
                'name' => 'delete_challenges_members_management',
                'display_name' => 'Delete Challenges Members Management',
                'description'=>'Delete Challenges Members Management',
            ], 
            
            [
                'name' => 'view_challenges_path',
                'display_name' => 'View Challenge Path',
                'description'=>'View Challenge Path',
            ], 
            [
                'name' => 'create_challenges_path',
                'display_name' => 'Create Challenge Path',
                'description'=>'Create Challenge Path',
            ], 
            [
                'name' => 'edit_challenges_path',
                'display_name' => 'Edit Challenge Path',
                'description'=>'Edit Challenge Path',
            ], 
            [
                'name' => 'delete_challenges_path',
                'display_name' => 'Delete Challenge Path',
                'description'=>'Delete Challenge Path',
            ],
            [
                'name' => 'view_resource_module_management',
                'display_name' => 'View Resource Module Management',
                'description'=>'View Resource Module Management',
            ], 
            [
                'name' => 'create_resource_module_management',
                'display_name' => 'Create Resource Module Management',
                'description'=>'Create Resource Module Management',
            ], [
                'name' => 'edit_resource_module_management',
                'display_name' => 'Edit Resource Module Management',
                'description'=>'Edit Resource Module Management',
            ], 
            [
                'name' => 'delete_resource_module_management',
                'display_name' => 'Delete Resource Module Management',
                'description'=>'Delete Resource Module Management',
            ],
            [
                'name' => 'view_resource_collection_management',
                'display_name' => 'View Resource Collection Management',
                'description'=>'View Resource Collection Management',
            ], 
            [
                'name' => 'create_resource_collection_management',
                'display_name' => 'Create Resource Collection Management',
                'description'=>'Create Resource Collection Management',
            ], [
                'name' => 'edit_resource_collection_management',
                'display_name' => 'Edit Resource Collection Management',
                'description'=>'Edit Resource Collection Management',
            ], 
            [
                'name' => 'delete_resource_collection_management',
                'display_name' => 'Delete Resource Collection Management',
                'description'=>'Delete Resource Collection Management',
            ], 
            [
                'name' => 'view_resource_group_management',
                'display_name' => 'View Resource Group Management',
                'description'=>'View Resource Group Management',
            ], 
            [
                'name' => 'create_resource_group_management',
                'display_name' => 'Create Resource Group Management',
                'description'=>'Create Resource Group Management',
            ], [
                'name' => 'edit_resource_group_management',
                'display_name' => 'Edit Resource Group Management',
                'description'=>'Edit Resource Group Management',
            ], 
            [
                'name' => 'delete_resource_group_management',
                'display_name' => 'Delete Resource Group Management',
                'description'=>'Delete Resource Group Management',
            ], 
            [
                'name' => 'subscription_management_billing_managment',
                'display_name' => 'Subscription Management Billing Managment',
                'description'=>'Subscription Management Billing Managment',
            ], 
            [
                'name' => 'impersonate_user_impersonate_user',
                'display_name' => 'Impersonate User Impersonate User',
                'description'=>'Impersonate User Impersonate User',
            ], 
                     
           ];

           foreach ($permission as $key => $permission_array){
            Permission::updateOrCreate(
                ['name' =>  $permission_array['name']],
                ['display_name' => $permission_array['display_name']],
                ['description' => $permission_array['description']],
            );
        }
    }
}
