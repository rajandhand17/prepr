<?php

namespace App\Services\Maestro;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public static function getUserCounts()
    {
        try {
            return User::count();
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getUserById($id)
    {
        try {
            $user = User::find($id);
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateUserById($id, $request)
    {
        try {
            $user = User::findOrFail($id);

            if (!empty($user)) {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->full_name = $request->first_name.' '.$request->last_name;
                $user->username = $request->username;
                $user->is_deactivated = $request->status;
                $user->verified_user = $request->verified_user;
                $user->email = $request->email;
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
                if ($user->save()) {
                    return $user;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteUser($id)
    {
        try {
            $user = User::find($id);
            if (!empty($user)) {
                return $user->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createUser($request)
    {
        try {
            $createUser = User::create(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'full_name' => $request->first_name.' '.$request->last_name, 'username' => $request->username, 'email' => $request->email,'is_deactivated' => $request->status,'verified_user' => $request->verified_user, 'password' => Hash::make($request->password)]);
            if (!empty($createUser)) {
                return $createUser;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUsers()
    {
        try {
            return User::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
