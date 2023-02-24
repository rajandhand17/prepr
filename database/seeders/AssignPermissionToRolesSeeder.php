<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        if($all_permissions){
            //Assign All permissions to Super Admin
            $super_admin = Role::where('name','super_admin')->first();
            if($super_admin){
                $super_admin->syncPermissions($all_permissions);
            }

            //Assign Permissions to organization owner
            $organization_owner = Role::where('name','organization_owner')->first();
            if($organization_owner){
                $remove_permissions = ['impersonate_user'];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission){
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission){
                        return $value->name === $permission;
                    });
                }
                $organization_owner->syncPermissions($final_permissions);
            }

            //Assign Permissions to organization manager
            $organization_manager = Role::where('name','organization_manager')->first();
            if($organization_manager){
                $remove_permissions = ['impersonate_user'];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission){
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission){
                        return $value->name === $permission;
                    });
                }
                $organization_manager->syncPermissions($final_permissions);
            }


            //Assign Permissions to lab manager
            $lab_manager = Role::where('name','lab_manager')->first();
            if($lab_manager){
                $remove_permissions = [
                    'impersonate_user',
                    'view_organization',
                    'create_organization',
                    'edit_organization',
                    'delete_organization',
                    'view_organization_members',
                    'create_organization_members',
                    'edit_organization_members',
                    'delete_organization_members'
                ];
                $final_permissions = $all_permissions;
                foreach ($remove_permissions as $permission){
                    $final_permissions = $final_permissions->reject(function ($value, $key) use ($permission){
                        return $value->name === $permission;
                    });
                }
                $lab_manager->syncPermissions($final_permissions);
            }
        }


    }
}
