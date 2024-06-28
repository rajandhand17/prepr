<?php

namespace App\Services;

use App\Models\PartnerCompanies;

class PartnerCompaniesService
{
    public function getPartnerCompanies()
    {
        try {
            return PartnerCompanies::where('status','publish')->get()->take(10);
        } catch (\Exception $e) {
            return false;
        }
    }
}
