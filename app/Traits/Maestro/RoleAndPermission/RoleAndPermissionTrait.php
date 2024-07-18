<?php

namespace App\Traits\Maestro\RoleAndPermission;

use App\Services\Maestro\RoleAndPermissionService;
use Illuminate\Support\Facades\DB;
use Exception;

trait RoleAndPermissionTrait
{
    private function createRole($request)
    {
        try {
            $createRole = DB::transaction(function () use ($request) {
                $role = RoleAndPermissionService::createRole($request);
                $roleSync = $role->syncPermissions(!empty($request->permission) ? $request->permission : []);
                return [
                    'role' => $role,
                    'role_sync' => $roleSync
                ];
            });

            if ($createRole['role'] && $createRole['role_sync']) {
                DB::commit();
                return $createRole['role'];
            }
            DB::rollBack();
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getPermissionBYRoleId($id)
    {
        try {
            $permissions = RoleAndPermissionService::getPermissionBYRoleId($id);
            if (!empty($permissions)) {
                return $permissions;
            }

            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getRoleById($id)
    {
        try {
            $roles = RoleAndPermissionService::getRole($id);
            if (!empty($roles)) {
                return $roles;
            }

            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getPermissions()
    {
        try {
            $permissions = RoleAndPermissionService::permissions();
            if (!empty($permissions)) {
                return $permissions;
            }

            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function updateRole($id, $request)
    {
        try {
            $updateRole = DB::transaction(function () use ($id, $request) {
                $role = RoleAndPermissionService::updateRole($id, $request);
                $roleSync = $role->syncPermissions(!empty($request->permission) ? $request->permission : []);
                return [
                    'role' => $role,
                    'role_sync' => $roleSync
                ];
            });

            if ($updateRole['role'] && $updateRole['role_sync']) {
                DB::commit();
                return $updateRole['role'];
            }
            DB::rollBack();
            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getRoles()
    {
        try {
            $roles = RoleAndPermissionService::getRoles();
            if (!empty($roles)) {
                return $roles;
            }
            return [];
        } catch (Exception $e) {
            return [];
        }
    }
}
