<?php

namespace App\Services\Manage;

use App\Models\ResourceModuleRating;

class ResourceModuleRatingService
{
    public static function delete($resource_module_id)
    {
        try {
            $resourceModuleRating = ResourceModuleRating::where('resource_module_id', $resource_module_id)->first();
            if ($resourceModuleRating !== null) {
                return $resourceModuleRating->delete();
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
