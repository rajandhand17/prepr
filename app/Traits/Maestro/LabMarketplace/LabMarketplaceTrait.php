<?php


namespace App\Traits\Maestro\LabMarketplace;

use App\Services\Maestro\LabMarketplace\LabMarketplaceService;
use App\Services\Maestro\Setting\SettingService;
use App\Services\Maestro\User\UserService;
use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
use Exception;

trait LabMarketplaceTrait
{
    private function getLabMarketplace()
    {
        try {
            $settings = LabMarketplaceService::getLabMarketplace();
            if($settings){
                return $settings;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }
}
