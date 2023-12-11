<?php

namespace App\Services\Public;

use App\Helpers\LanguageColumnHelper;
use App\Models\Country;
use DB;
use Illuminate\Support\Facades\Schema;

class ProfileService
{
    public function getCountriesList($language, $search)
    {
        try {
            if ($language == 'en') {
                $country_list = Country::select('id', 'title');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('countries', $column_name)) {
                    return false;
                }
                $country_list = Country::select('id', $column_name.' as title');
            }

            return $country_list->get();
        } catch(\Exception $e) {
            return false;
        }
    }
}
