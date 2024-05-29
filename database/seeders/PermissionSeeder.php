<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            [
                'name'         => 'change_organization_ownership',
                'display_name' => 'Change Organization Ownership',
                'description'  => 'Organization Owner can assign their ownership to another user with in the organization',
            ],
            [
                'name'         => 'view_organization',
                'display_name' => 'Organization View',
                'description'  => 'Organization view permission to who can view organization',
            ], [
                'name'         => 'create_organization',
                'display_name' => 'Create Organization',
                'description'  => 'Organization create permission to who can create organization',
            ],
            [
                'name'         => 'edit_organization',
                'display_name' => 'Edit Organization',
                'description'  => 'Organization edit permission to who can edit organization',
            ],
            [
                'name'         => 'delete_organization',
                'display_name' => 'Delete Organization',
                'description'  => 'Organization delete permission to who can delete organization',
            ],
            [
                'name'         => 'view_organization_members',
                'display_name' => 'View Organization Member',
                'description'  => 'Allow to user to view organization member',
            ],
            [
                'name'         => 'create_organization_members',
                'display_name' => 'Create Organization Member',
                'description'  => 'Allow to user to create organization member',
            ],
            [
                'name'         => 'edit_organization_members',
                'display_name' => 'Edit Organization Member',
                'description'  => 'Allow to user to edit organization member',
            ],
            [
                'name'         => 'delete_organization_members',
                'display_name' => 'Delete Organization Member Manager',
                'description'  => 'Allow to user to delete organization member',
            ],
            [
                'name'         => 'view_lab',
                'display_name' => 'View Lab',
                'description'  => 'Allow to user to view lab',
            ],
            [
                'name'         => 'create_lab',
                'display_name' => 'Create Lab',
                'description'  => 'Allow to user to create lab',
            ],
            [
                'name'         => 'edit_lab',
                'display_name' => 'Edit Lab',
                'description'  => 'Allow to user to edit lab',
            ],
            [
                'name'         => 'delete_lab',
                'display_name' => 'Delete Lab',
                'description'  => 'Allow to user to delete lab',
            ],
            [
                'name'         => 'generate_lab_report',
                'display_name' => 'Generate Lab Report',
                'description'  => 'Allow to user to generate lab report',
            ],
            [
                'name'         => 'view_lab_member',
                'display_name' => 'View Lab Member',
                'description'  => 'Allow to user to view lab member',
            ],
            [
                'name'         => 'create_lab_member',
                'display_name' => 'Create Lab Member',
                'description'  => 'Allow to create lab member',
            ],
            [
                'name'         => 'edit_lab_member',
                'display_name' => 'Edit Lab Member',
                'description'  => 'Allow user to edit lab member',
            ],
            [
                'name'         => 'delete_lab_member',
                'display_name' => 'Delete Lab Memeber',
                'description'  => 'Allow user to delete lab member',
            ], [
                'name'         => 'view_lab_programs',
                'display_name' => 'View Lab Programs',
                'description'  => 'Allow user to view lab program',
            ],
            [
                'name'         => 'create_lab_programs',
                'display_name' => 'Create Lab Programs',
                'description'  => 'Allow user to create lab program',
            ],
            [
                'name'         => 'edit_lab_programs',
                'display_name' => 'Edit Lab Programs',
                'description'  => 'Allow user to edit lab program',
            ],
            [
                'name'         => 'delete_lab_programs',
                'display_name' => 'Delete Lab programs',
                'description'  => 'Allow user to delete lab program',
            ],
            [
                'name'         => 'view_challenge',
                'display_name' => 'View Challenge',
                'description'  => 'Allow to user to challenge',
            ],
            [
                'name'         => 'create_challenge',
                'display_name' => 'Create Challenge',
                'description'  => 'Allow user to create challenge',
            ],
            [
                'name'         => 'edit_challenge',
                'display_name' => 'Edit Challenge',
                'description'  => 'Allow user to edit challenge',
            ],
            [
                'name'         => 'delete_challenge',
                'display_name' => 'Delete Challenge',
                'description'  => 'Allow user to delete challenge',
            ],
            [
                'name'         => 'generate_challenge_report',
                'display_name' => 'Generate Challenge Report',
                'description'  => 'Allow user to generate challenge report',
            ],
            [
                'name'         => 'clone_challenge',
                'display_name' => 'Clone Challenge',
                'description'  => 'Allow user to create clone challenge',
            ],

            [
                'name'         => 'create_challenge_annoucements',
                'display_name' => 'Create Challenge Annoucements',
                'description'  => 'Allow user to create challenge annoucements',
            ],
            [
                'name'         => 'edit_challenge_annoucements',
                'display_name' => 'Edit Challenge Annoucements',
                'description'  => 'Allow user to edit challenge annoucements',
            ],
            [
                'name'         => 'delete_challenge_annoucements',
                'display_name' => 'Delete Challenge Annoucements',
                'description'  => 'Allow user to challenge annoucements',
            ],
            [
                'name'         => 'list_challenge_annoucements',
                'display_name' => 'List Challenge Annoucements',
                'description'  => 'Allow user to list challenge annoucements',
            ],
            [
                'name'         => 'create_challenge_assessment',
                'display_name' => 'Create Challenge Assessment',
                'description'  => 'Allow user to create challenge assessment',
            ],
            [
                'name'         => 'update_challenge_assessment',
                'display_name' => 'Update Challenge Assessment',
                'description'  => 'Allow user to update challenge assessment',
            ],
            [
                'name'         => 'view_challenge_assessment',
                'display_name' => 'View Challenge Assessment',
                'description'  => 'Allow user to view challenge assessment',
            ],
            [
                'name'         => 'view_project_submission',
                'display_name' => 'View Project Submission',
                'description'  => 'Allow user to view project sumission',
            ],
            [
                'name'         => 'select_challenge_winner',
                'display_name' => 'Select Challenge Winner',
                'description'  => 'Allow manager to select winner of the challenge',
            ],
            [
                'name'         => 'remove_project_submission',
                'display_name' => 'Remove Project Submission',
                'description'  => 'Allow user to remove project submission',
            ],
            [
                'name'         => 'view_challenges_members',
                'display_name' => 'View Challenges Members',
                'description'  => 'Allow user to view challenges members',
            ],

            [
                'name'         => 'create_challenges_members',
                'display_name' => 'Create Challenges Members',
                'description'  => 'Allow user to create challenges members',
            ],
            [
                'name'         => 'edit_challenges_members',
                'display_name' => 'Edit Challenges Members',
                'description'  => 'Allow user to edit challenges members',
            ],
            [
                'name'         => 'delete_challenges_members',
                'display_name' => 'Delete Challenges Members',
                'description'  => 'Allow user to delete challenges members',
            ],
            [
                'name'         => 'view_challenges_path',
                'display_name' => 'View Challenge Path',
                'description'  => 'Allow user to view challenge path',
            ],
            [
                'name'         => 'create_challenges_path',
                'display_name' => 'Create Challenge Path',
                'description'  => 'Allow user to create challenge path',
            ],
            [
                'name'         => 'edit_challenges_path',
                'display_name' => 'Edit Challenge Path',
                'description'  => 'Allow user to edit challenge path',
            ],
            [
                'name'         => 'delete_challenges_path',
                'display_name' => 'Delete Challenge Path',
                'description'  => 'Allow user to delete challenge path',
            ],
            [
                'name'         => 'view_resource_module',
                'display_name' => 'View Resource Module',
                'description'  => 'Allow user to view resource module',
            ],
            [
                'name'         => 'create_resource_module',
                'display_name' => 'Create Resource Module',
                'description'  => 'Allow user to create resource module',
            ], [
                'name'         => 'edit_resource_module',
                'display_name' => 'Edit Resource Module',
                'description'  => 'Allow user to edit resource module',
            ],
            [
                'name'         => 'delete_resource_module',
                'display_name' => 'Delete Resource Module',
                'description'  => 'Allow user to delete resource module',
            ],
            [
                'name'         => 'view_resource_collection',
                'display_name' => 'View Resource Collection',
                'description'  => 'Allow user to view resource collection',
            ],
            [
                'name'         => 'create_resource_collection',
                'display_name' => 'Create Resource Collection',
                'description'  => 'Allow user to create resource collection',
            ], [
                'name'         => 'edit_resource_collection',
                'display_name' => 'Edit Resource Collection',
                'description'  => 'Allow user to edit resource Collection',
            ],
            [
                'name'         => 'delete_resource_collection',
                'display_name' => 'Delete Resource Collection',
                'description'  => 'Allow user to delete resource collection',
            ],
            [
                'name'         => 'view_resource_group',
                'display_name' => 'View Resource Group',
                'description'  => 'Allow user to view resource group',
            ],
            [
                'name'         => 'create_resource_group',
                'display_name' => 'Create Resource Group',
                'description'  => 'Allow user to create resource group',
            ], [
                'name'         => 'edit_resource_group',
                'display_name' => 'Edit Resource Group',
                'description'  => 'Allow user to edit resource group',
            ],
            [
                'name'         => 'delete_resource_group',
                'display_name' => 'Delete Resource Group',
                'description'  => 'Allow user to delete resource group',
            ],
            [
                'name'         => 'impersonate_user',
                'display_name' => 'Impersonate User',
                'description'  => "One User can switch login into another user's account",
            ],
            [
                'name'         => 'create_resource_module_from_go1',
                'display_name' => 'Create Resource Module From go1',
                'description'  => 'Allow user to create resource from go1',
            ],

            [
                'name'         => 'can_send_live_event_invitation_lab',
                'display_name' => 'Send live event invitation link to lab members via email.',
                'description'  => 'Allow user to send live event invitation link to lab members via email ',
            ],
            [
                'name'         => 'can_join_live_event_lab',
                'display_name' => 'Join live event associated with labs.',
                'description'  => 'Allow user to join live event associated with labs.',
            ],
        ];

        foreach ($permission as $key => $permission_array) {
            Permission::updateOrCreate(
                [
                    'name' => $permission_array['name'],
                ],
                [
                    'display_name' => $permission_array['display_name'],
                    'description'  => $permission_array['description'],
                ],
            );
        }
    }
}
