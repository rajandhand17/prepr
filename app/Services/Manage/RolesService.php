<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\Role;

class RolesService
{
    public function getRoles($role_type)
    {
        try {
            $getRoles = Role::select('display_name');
            switch ($role_type) {
                case 0:
                    $getRoles = $getRoles->where('role_type', $role_type);
                    break;
                case 1:
                    $getRoles = $getRoles->where('role_type', $role_type);
                    break;
                default:
                    $getRoles = $getRoles->where('role_type', config('constants.role_type.external'));
            }

            return $getRoles->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRoleBasedOnDisplayName($role_name)
    {
        try {
            $getRoles = Role::where('display_name', $role_name)->first();
            if ($getRoles) {
                return $getRoles;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getAllRoles()
    {
        try {
            return Role::where('role_type', '1')->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
