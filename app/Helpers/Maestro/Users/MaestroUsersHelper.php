<?php

namespace App\Helpers\Maestro\Users;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class MaestroUsersHelper
{
    public static function getUserById($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUserForTableBuilder()
    {
        try {
            return User::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createUser($request)
    {
        try {
            $createUser = User::create(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'full_name' => $request->first_name . ' ' . $request->last_name, 'username' => $request->username, 'email' => $request->email, 'password' => Hash::make($request->password)]);
            if (!empty($createUser)) {
                $createUser->attachRole('user');
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateUser($user, $request)
    {
        try {
            if (!empty($user)) {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->full_name = $request->first_name . ' ' . $request->last_name;
                $user->username = $request->username;
                $user->email = $request->email;
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }
                if ($user->save()) {
                    return true;
                }
                return false;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
