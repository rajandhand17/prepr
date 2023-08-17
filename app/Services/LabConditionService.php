<?php

namespace App\Services;

use App\Models\LabCondition;

class LabConditionService
{
    public function getLabConditions($language = 'en', $search = null)
    {
        try {
            $labConditions = LabCondition::select('id', 'title');
            if ($search != null) {
                $labConditions = $labConditions->where('title', 'like', '%' . $search . '%');
            }
            $labConditions = $labConditions->take(20)->get();
            //  return $host;
            if (!$labConditions->isEmpty()) {
                return $labConditions;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
