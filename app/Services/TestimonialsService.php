<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\Testimonials;

class TestimonialsService
{
    public function getUsers()
    {
        try {
            return Testimonials::where('status', '1')->get()->take(3);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
