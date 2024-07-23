<?php

namespace App\Services\Public;

use App\Models\ResourceGroupRating;
use Exception;

class ResourceGroupRatingService
{
    public static function getResourceGroupBasedOnRating($rating)
    {
        try {
            $resourceGroupRating=ResourceGroupRating::where("rating",$rating)->get();
            return $resourceGroupRating;
        }catch(Exception $e) {
            return false;
        }
    }
}
