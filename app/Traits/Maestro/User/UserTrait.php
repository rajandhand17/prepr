<?php

namespace App\Traits\Maestro\User;

use App\Services\Maestro\UserService;
use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
use Exception;
use Illuminate\Support\Facades\DB;

trait UserTrait
{
    private function createUser($request)
    {
        try {
            $createUser = DB::transaction(function () use ($request) {
                $user = UserService::createUser($request);
                $role = RoleAndPermissionService::userSyncRoles($user, $request->roles);
                return [
                    'user' => $user,
                    'role' => $role
                ];
            });

            if ($createUser['user'] && $createUser['role']) {
                DB::commit();
                return $createUser['user'];
            }
            DB::rollBack();
            return false;
        } catch (Exception $e) {
            DB::rollBack();
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
            $updateUser = DB::transaction(function () use ($id, $request) {
                            $user = UserService::updateUserById($id, $request);
                            $role = RoleAndPermissionService::userSyncRoles($user, $request->roles);
                            return [
                                'user' => $user,
                                'role' => $role
                            ];
                        });

            if ($updateUser['user'] && $updateUser['role']) {
                DB::commit();
                return $updateUser['user'];
            }
            DB::rollBack();
            return false;
        } catch (Exception $e) {
            DB::rollBack();
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
}
