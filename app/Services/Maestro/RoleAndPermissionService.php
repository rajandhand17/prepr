<?php

namespace App\Services\Maestro;

use App\Models\Permission;
use App\Models\Role;
use Exception;

class RoleAndPermissionService
{
    public static function getPermissionBYRoleId($id)
    {
        try {
            $role = Role::find($id);
            $role_permission = $role->permissions()->pluck('id')->toArray();
            if (!empty($role_permission)) {
                return $role_permission;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function permissions()
    {
        try {
            $permissions = Permission::orderBy('id', 'asc')->get();
            if (!empty($permissions)) {
                return $permissions;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getRole($id)
    {
        try {
            $roles = Role::find($id);
            if (!empty($roles)) {
                return $roles;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createRole($request)
    {
        try {
            $role = Role::create(['name' => strtolower(str_replace(' ', '_', trim($request->display_name))), 'display_name' => trim($request->display_name), 'description' => trim($request->description)]);
            if ($role) {
                return $role;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getRoles()
    {
        try {
            return Role::query();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateRole($id, $request)
    {
        try {
            $role = Role::find($id);
            if ($role) {
                $role->name = $request->name;
                $role->save();

                return $role;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getAllRoles()
    {
        try {
            return Role::get();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function userSyncRoles($user, $roles)
    {
        try {
            if (!empty($roles)) {
                $user->syncRoles($roles);

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
