<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\FeaturedLab;

class FeaturedLabService
{
    public function getFeaturedLab()
    {
        try {
            return FeaturedLab::get()->take(6);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
