<?php

namespace App\Services\Manage;

use App\Models\User;

class ProfileService
{
    public static function getProfileBasedOnUserId($user_name)
    {
        try {
            $profile_list = User::where('username', $user_name)->first();
            return $profile_list;
        } catch(\Exception $e) {
            return false;
        }
    }
}
