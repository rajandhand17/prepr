<?php

namespace App\Services;

use App\Models\Testimonials;

class TestimonialsService
{
    public function getUsers()
    {
        try {
            return Testimonials::get()->take(4);
        } catch (\Exception $e) {
            return false;
        }
    }
}
