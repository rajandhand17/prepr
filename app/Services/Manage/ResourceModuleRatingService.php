<?php

namespace App\Services\Manage;

use App\Models\ResourceModule;
use App\Models\ResourceModuleRating;
use App\Models\ResourceModuleSkillsGroupsStack;

class ResourceModuleRatingService
{
    public static function deleteResourceModuleRating($resource_module_id)
    {
        try {
            $resourceModuleRating=ResourceModuleRating::where('resource_module_id', $resource_module_id)->first();
            if($resourceModuleRating!==null){
                return ResourceModuleRating::where('resource_module_id', $resource_module_id)->delete();
            }
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
