<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\PartnerCompanies;

class PartnerCompaniesService
{
    public function getPartnerCompanies()
    {
        try {
            return PartnerCompanies::where('status', 'publish')->get()->take(10);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
