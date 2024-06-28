<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\Levels;
use App\Models\PartnerCompanies;
use App\Models\Testimonials;
use Illuminate\Support\Facades\Schema;

class TestimonialsService
{
    public function getUsers()
    {
        try {
            return Testimonials::get()->take(4);
        }catch (\Exception $e) {
            return false;
        }
    }
}
