<?php

namespace App\Services\Manage;

use App\Models\ResourceModuleSkillsGroupsStack;

class ResourceModuleSkillsGroupsStackService
{
    public static function delete($resource_module_id)
    {
        try {
            $resourceModuleSkillsGroupsStack = ResourceModuleSkillsGroupsStack::where('resource_module_id', $resource_module_id)->first();
            if ($resourceModuleSkillsGroupsStack !== null) {
                return $resourceModuleSkillsGroupsStack->delete();
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
