<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\FlexibleExpireDateDuration;
use Illuminate\Support\Facades\Schema;

class FlexibleExpireDateDurationService
{
    public function getFlexibleDateDurations($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $flexible_date_duration = FlexibleExpireDateDuration::select('id', 'title');
            //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('flexible_expire_date_durations', $column_name)) {
                    return false;
                }
                $flexible_date_duration = FlexibleExpireDateDuration::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $flexible_date_duration = $flexible_date_duration->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $flexible_date_duration = $flexible_date_duration->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$flexible_date_duration->isEmpty()) {
                return $flexible_date_duration;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
