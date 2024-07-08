<?php

namespace App\Traits\Maestro\AutoCreateTemplate;

use App\Models\Role;
use App\Services\Maestro\AutoCreateTemplates\AutoCreateTemplatesService;
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

    public function getLists($request)
    {
        try {
            return AutoCreateTemplatesService::getList($request);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function fetchModuleList($request)
    {
        try {
            return AutoCreateTemplatesService::fetchModuleList($request);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function cloneModules($request)
    {
        try {
            return AutoCreateTemplatesService::cloneModule($request);
        }catch (\Exception $e){
            return false;
        }
    }
}
