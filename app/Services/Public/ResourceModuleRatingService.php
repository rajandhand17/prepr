<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleRating;

class ResourceModuleRatingService
{
    public static function addRating($resource_module_id, $request)
    {
        try {
            ResourceModuleRating::updateOrInsert([
                'resource_module_id'=> $resource_module_id,
                'user_id'           => auth()->user()->id,
            ], [
                'rating' => $request->rating,
            ]);

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
