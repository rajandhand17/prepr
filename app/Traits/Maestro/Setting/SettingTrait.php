<?php


namespace App\Traits\Maestro\Setting;

use App\Services\Maestro\User\UserService;
use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
use Exception;

trait SettingTrait
{
    private function updateSetting($id,$request)
    {
        try {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
