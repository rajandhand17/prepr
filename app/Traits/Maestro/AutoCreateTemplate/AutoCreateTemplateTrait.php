<?php

namespace App\Traits\Maestro\AutoCreateTemplate;

use App\Models\Role;
use App\Services\Manage\RolesService;

trait AutoCreateTemplateTrait
{
    public function getRole()
    {
        try {
            return RolesService::getAllRoles();
        }catch (\Exception $e) {
            return false;
        }
    }

}
