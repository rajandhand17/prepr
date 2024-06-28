<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\Levels;
use App\Models\PartnerCompanies;
use Illuminate\Support\Facades\Schema;

class PartnerCompaniesService
{
    public function getPartnerCompanies()
    {
        try {
            return PartnerCompanies::get()->take(32);
        }catch (\Exception $e) {
            return false;
        }
    }
}
