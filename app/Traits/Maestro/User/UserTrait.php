<?php

namespace App\Traits\Maestro\User;

use App\Services\Maestro\User\UserService;
use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
use Exception;

trait UserTrait
{
    private function createUser($request)
    {
        try {
            $userObj = UserService::createUser($request);
            if ($userObj) {
                RoleAndPermissionService::userSyncRoles($userObj, $request->roles);
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getUserById($id)
    {
        try {
            return UserService::getUserById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateUserById($id, $request)
    {
        try {
            $userObj = UserService::updateUserById($id, $request);
            if ($userObj) {
                RoleAndPermissionService::userSyncRoles($userObj, $request->roles);
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteUserById($id)
    {
        try {
            if (UserService::deleteUser($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getUsers()
    {
        try {
            $users = UserService::getUsers();
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getAllRoles()
    {
        try {
            $roles = RoleAndPermissionService::getAllRoles();
            if ($roles) {
                return $roles;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getAllPermissions()
    {
        try {
            $users = RoleAndPermissionService::permissions();
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
