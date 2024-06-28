<?php

namespace App\Services;

use App\Models\FeaturedLab;

class FeaturedLabService
{
    public function getFeaturedLab()
    {
        try {
            return FeaturedLab::get()->take(6);
        } catch (\Exception $e) {
            return false;
        }
    }
}
