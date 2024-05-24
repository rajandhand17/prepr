<?php

namespace App\Helpers\Maestro\Users;

use App\Models\User;

class MaestroUsersHelper
{
    public static function getUserList()
    {
        try {
            return User::get();
        } catch (\Exception $e) {
            return false;
        }
    }
}
