<?php

namespace App\Services;


use App\Models\FeaturedSkills;

class FeaturedSkillService
{
    public function getFeaturedSKill()
    {
        try {
            return FeaturedSkills::get()->take(12);
        }catch (\Exception $e) {
            return false;
        }
    }
}
