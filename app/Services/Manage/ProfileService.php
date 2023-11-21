<?php

namespace App\Services\Manage;

use App\Models\User;

class ProfileService
{
    public static function getProfileBasedOnUserId($user_name)
    {
        try {
            $profile = User::where('username', $user_name)->first();
            if($profile != null){
                return $profile;
            }
            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
