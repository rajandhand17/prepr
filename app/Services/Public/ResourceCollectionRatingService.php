<?php

namespace App\Services\Public;

use App\Models\ResourceCollectionRating;
use Exception;

class ResourceCollectionRatingService
{
    public static function getResourceCollectionBasedOnRating($rating)
    {
        try {
            $resourceCollectionRating=ResourceCollectionRating::where("rating",$rating)->get();
            return $resourceCollectionRating;
        }catch(Exception $e) {
            return false;
        }
    }
}
