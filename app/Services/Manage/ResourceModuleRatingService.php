<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleRating;

class ResourceModuleRatingService
{
    public static function deleteResourceModuleRating($resource_module_id)
    {
        try {
            ResourceModuleRating::where('resource_module_id', $resource_module_id)->delete();

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
