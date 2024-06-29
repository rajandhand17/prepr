<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\LabCondition;

class LabConditionService
{
    public function getLabConditions($language = 'en', $search = null)
    {
        try {
            $labConditions = LabCondition::select('id', 'title');
            if ($search != null) {
                $labConditions = $labConditions->where('title', 'like', '%'.$search.'%');
            }
            $labConditions = $labConditions->take(config('site-settings.dropdown_listing_limit'))->get();
            //  return $host;
            if (!$labConditions->isEmpty()) {
                return $labConditions;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
