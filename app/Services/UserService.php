<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public static function joinName($firstName, $lastName)
    {
        return $firstName.' '.$lastName;
    }

    public static function getUserByEmail($email)
    {
        try {
            $user = User::select([
                'id', 'preferred_language', 'first_name', 'last_name', 'full_name', 'username', 'email', 'country_code', 'phone_number',
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'is_profile_completed', 'created_at',
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
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'is_profile_completed', 'created_at', 'is_deactivated',
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
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'is_profile_completed', 'created_at',
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
            $user = $user->take(config('site-settings.pagination_per_page'))->get();

            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function addUserName($request)
    {
        try {
            $updateUser = User::where('id', auth()->user()->id)->first();
            $updateUser->full_name = $request->name;
            $updateUser->save();

            return $updateUser;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updataUserAccount($request)
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name.' '.$request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->preferred_language = $request->preferred_language;
            $user->preferred_timezone = $request->preferred_timezone;
            $user->two_factor_verification = ($request->two_factor_verification == true) ? '1' : '0';
            $user->save();

            return $user;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function changePassword($request)
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                return $user;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function removeProfileImage()
    {
        try {
            $user = User::find(auth()->user()->id);
            if ($user) {
                $user->profile_image = config('site-settings.default_user_profile_image');
                $user->save();

                return $user;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deactivateUserAccount()
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->is_deactivated = '1';
            $user->save();

            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }
}
