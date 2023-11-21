<?php

namespace App\Services\Manage;

use App\Models\User;

class ProfileService
{
    public static function userDetails($username)
    {
        try {
            $profile_list = User::where('username', $username)->first();

            return $profile_list;
        } catch(\Exception $e) {
            return false;
        }
    }
}
