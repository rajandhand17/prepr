<?php

namespace App\Traits\Maestro\Category;

use App\Services\Maestro\Category\CategoryService;
use Exception;

trait CategoryTrait
{
    private function createUser($request)
    {
        try {
            if(CategoryService::createUser($request)){
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
            return CategoryService::getUserById($id);
        } catch (Exception $e) {
            return false;
        }
    }
    private function updateUserById($id,$request)
    {
        try {
            if(CategoryService::updateUserById($id,$request)){
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
            if(CategoryService::deleteUser($id)){
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
            $users = CategoryService::getUsers();
            if($users){
                return $users;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
