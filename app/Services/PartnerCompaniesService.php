<?php

namespace App\Services;

use App\Models\PartnerCompanies;

class PartnerCompaniesService
{
    public function getPartnerCompanies()
    {
        try {
            return PartnerCompanies::get()->take(32);
        } catch (\Exception $e) {
            return false;
        }
    }
}
