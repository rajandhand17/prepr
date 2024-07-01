<?php

namespace App\Traits\Maestro\RoleAndPermission;

use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
use Exception;

trait RoleAndPermissionTrait
{
    private function createRole($request)
    {
        try {
            if (RoleAndPermissionService::createRole($request)) {
                return true;
            }

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
            $roleUpdated = RoleAndPermissionService::updateRole($id, $request);
            if (!empty($roleUpdated)) {
                return true;
            }

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
        } catch (Exception $e) {
            return false;
        }
    }
}
