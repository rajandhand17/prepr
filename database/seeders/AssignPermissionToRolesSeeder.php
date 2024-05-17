<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AssignPermissionToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_permissions = Permission::get();

        if ($all_permissions) {
            //Assign All permissions to Super Admin
            $super_admin = Role::where('name', 'super_admin')->first();
            if ($super_admin) {
                $super_admin->syncPermissions($all_permissions);
            }

            //Assign Permissions to organization owner
            $organization_owner = Role::where('name', 'organization_owner')->first();
            if ($organization_owner) {
                $remove_permissions = ['impersonate_user'];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission) {
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission) {
                        return $value->name === $permission;
                    });
                }
                $organization_owner->syncPermissions($final_permissions);
            }

            //Assign Permissions to organization manager
            $organization_manager = Role::where('name', 'organization_manager')->first();
            if ($organization_manager) {
                $remove_permissions = [
                    'impersonate_user',
                    'change_organization_ownership',
                    'view_organization',
                    'create_organization',
                    'edit_organization',
                    'delete_organization',
                ];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission) {
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission) {
                        return $value->name === $permission;
                    });
                }
                $organization_manager->syncPermissions($final_permissions);
            }

            //Assign Permissions to lab manager
            $lab_manager = Role::where('name', 'lab_manager')->first();
            if ($lab_manager) {
                $remove_permissions = [
                    'impersonate_user',
                    'change_organization_ownership',
                    'view_organization',
                    'create_organization',
                    'edit_organization',
                    'delete_organization',
                    'view_organization_members',
                    'create_organization_members',
                    'edit_organization_members',
                    'delete_organization_members',
                ];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission) {
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission) {
                        return $value->name === $permission;
                    });
                }
                $lab_manager->syncPermissions($final_permissions);
            }

            //Assign Permissions To Challenge Manager
            $lab_manager = Role::where('name', 'challenge_manager')->first();
            if ($lab_manager) {
                $remove_permissions = [
                    'impersonate_user',
                    'change_organization_ownership',
                    'view_organization',
                    'create_organization',
                    'edit_organization',
                    'delete_organization',
                    'view_organization_members',
                    'create_organization_members',
                    'edit_organization_members',
                    'delete_organization_members',
                    'view_lab',
                    'create_lab',
                    'edit_lab',
                    'delete_lab',
                    'generate_lab_report',
                    'view_lab_member',
                    'create_lab_member',
                    'edit_lab_member',
                    'delete_lab_member',
                    'view_lab_programs',
                    'create_lab_programs',
                    'edit_lab_programs',
                    'delete_lab_programs',
                    'can_send_live_event_invitation_lab',
                    'can_join_live_event_lab',
                ];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission) {
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission) {
                        return $value->name === $permission;
                    });
                }
                $lab_manager->syncPermissions($final_permissions);
            }

            //Assign Permissions To Resource Manager
            $lab_manager = Role::where('name', 'resource_manager')->first();
            if ($lab_manager) {
                $remove_permissions = [
                    'impersonate_user',
                    'change_organization_ownership',
                    'view_organization',
                    'create_organization',
                    'edit_organization',
                    'delete_organization',
                    'view_organization_members',
                    'create_organization_members',
                    'edit_organization_members',
                    'delete_organization_members',
                    'view_lab',
                    'create_lab',
                    'edit_lab',
                    'delete_lab',
                    'generate_lab_report',
                    'view_lab_member',
                    'create_lab_member',
                    'edit_lab_member',
                    'delete_lab_member',
                    'view_lab_programs',
                    'create_lab_programs',
                    'edit_lab_programs',
                    'delete_lab_programs',
                    'view_challenge',
                    'create_challenge',
                    'edit_challenge',
                    'delete_challenge',
                    'generate_challenge_report',
                    'clone_challenge',
                    'view_challenge_annoucements',
                    'create_challenge_annoucements',
                    'edit_challenge_annoucements',
                    'delete_challenge_annoucements',
                    'create_challenge_assessment',
                    'view_project_submission',
                    'remove_project_submission',
                    'view_challenges_member',
                    'create_challenges_member',
                    'edit_challenges_member',
                    'delete_challenges_member',
                    'view_challenges_path',
                    'create_challenges_path',
                    'edit_challenges_path',
                    'delete_challenges_path',
                    'can_send_live_event_invitation_lab',
                    'can_join_live_event_lab',
                ];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission) {
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission) {
                        return $value->name === $permission;
                    });
                }
                $lab_manager->syncPermissions($final_permissions);
            }

            //Assign Permissions to lab manager
            $lab_manager = Role::where('name', 'customer_success')->first();
            if ($lab_manager) {
                $remove_permissions = [
                    'delete_organization',
                    'change_organization_ownership',
                    'delete_organization_members',
                    'delete_lab',
                    'delete_lab_member',
                    'delete_lab_programs',
                    'delete_challenge',
                    'delete_challenge_annoucements',
                    'delete_project_submission',
                    'delete_challenges_members',
                    'delete_challenges_path',
                    'delete_resource_module',
                    'delete_resource_collection',
                    'delete_resource_group',
                ];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission) {
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission) {
                        return $value->name === $permission;
                    });
                }
                $lab_manager->syncPermissions($final_permissions);
            }
        }
    }
}
