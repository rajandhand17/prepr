<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function joinName($firstName, $lastName){
        return $firstName.' '.$lastName;
    }
    public static function getUserByEmail($email)
    {
        try {
            $user = User::select([
                'id', 'preferred_language', 'first_name', 'last_name', 'full_name', 'username', 'email', 'country_code', 'phone_number',
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'referal_code', 'is_profile_completed', 'created_at',
            ])->where('email', $email)->first();
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getUserById($id)
    {
        try {
            $user = User::select([
                'id', 'preferred_language', 'first_name', 'last_name', 'full_name', 'username', 'email', 'country_code', 'phone_number',
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'referal_code', 'is_profile_completed', 'created_at',
            ])->find($id);
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getUserByUsername($username)
    {
        try {
            $user = User::select([
                'id', 'preferred_language', 'first_name', 'last_name', 'full_name', 'username', 'email', 'country_code', 'phone_number',
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'referal_code', 'is_profile_completed', 'created_at',
            ])->where('username', $username)->first();
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getUsers($request)
    {
        try {
            $user = User::select();
            if ($request->search) {
                $user = $user->orWhere('full_name', 'like', '%'.$request->search.'%')->orWhere('username', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%');
            }
            $user = $user->get();
            if ($user->count() > 0) {
                return $user;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
