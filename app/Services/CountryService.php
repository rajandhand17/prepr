<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Country;
use Illuminate\Support\Facades\Schema;

class CountryService
{
    public function getCountries($request)
    {
        try {
            if ($request->language == 'en') {
                $country_list = Country::select('id', 'title');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($request->language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('countries', $column_name)) {
                    return false;
                }
                $country_list = Country::select('id', $column_name.' as title');
            }

            return $country_list->get();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
