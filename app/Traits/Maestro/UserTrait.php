<?php

namespace App\Traits\Maestro;

use App\Services\Maestro\UserService;
use Exception;

trait UserTrait
{
    private function createUser($request)
    {
        try {
            if(UserService::createUser($request)){
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
    private function updateUserById($id,$request)
    {
        try {
            if(UserService::updateUserById($id,$request)){
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
            if(UserService::deleteUser($id)){
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
            if($users){
                return $users;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
