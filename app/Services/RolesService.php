<?php

namespace App\Services;

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
            return false;
        }
    }

}
