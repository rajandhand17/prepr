<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\Institutions;
use Illuminate\Support\Facades\Schema;

class InstitutionService
{
    public function getInstitutionsList($language, $search)
    {
        try {
            if ($language == 'en') {
                $country_list = Institutions::select('id', 'title');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('institutions', $column_name)) {
                    return false;
                }
                $country_list = Institutions::select('id', $column_name.' as title');
            }

            return $country_list->get();
        } catch(\Exception $e) {
            return false;
        }
    }
}
