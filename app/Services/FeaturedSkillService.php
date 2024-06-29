<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\FeaturedSkills;

class FeaturedSkillService
{
    public function getFeaturedSKill()
    {
        try {
            return FeaturedSkills::get()->take(12);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
