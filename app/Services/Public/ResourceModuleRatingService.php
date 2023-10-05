<?php

namespace App\Services\Public;

use App\Models\ResourceModuleRating;

class ResourceModuleRatingService
{
    public static function addReview($resource_module_id, $request)
    {
        try {
            $resourceModuleRating = new ResourceModuleRating();
            $resourceModuleRating->resource_module_id = $resource_module_id;
            $resourceModuleRating->user_id = auth()->user()->id;
            $resourceModuleRating->rating = $request->review;
            $resourceModuleRating->save();

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
