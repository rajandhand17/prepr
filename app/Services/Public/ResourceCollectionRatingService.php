<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionRating;
use Exception;

class ResourceCollectionRatingService
{
    public static function getResourceCollectionBasedOnRating($rating)
    {
        try {
            $resourceCollectionRating = ResourceCollectionRating::where('rating', $rating)
                ->where('user_id', auth()->user()->id)
                ->get();

            return $resourceCollectionRating;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
