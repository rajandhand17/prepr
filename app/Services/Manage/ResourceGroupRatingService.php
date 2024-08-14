<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceGroupRating;
use Exception;

class ResourceGroupRatingService
{
    public static function getResourceGroupBasedOnRating($rating)
    {
        try {
            $resourceGroupRating = ResourceGroupRating::where('rating', $rating)
                ->where('user_id', auth()->user()->id)
                ->get();

            return $resourceGroupRating;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
