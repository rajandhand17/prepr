<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Duration;
use Illuminate\Support\Facades\Schema;

class DurationService
{
    public static function getDurations($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $durations = Duration::select('id', 'title');
            } else {
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not

                if (!$column_name || !Schema::hasColumn('durations', $column_name)) {
                    return false;
                }
                $durations = Duration::select('id', $column_name.' as title');
            }

            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $durations = $durations->where($column_name, 'like', '%'.$search.'%');
            }
            $durations = $durations->take(config('site-settings.dropdown_listing_limit'))->get();
            if (!$durations->isEmpty()) {
                return $durations;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getDurationsBasedOnId($durationId)
    {
        try {
            return Duration::find($durationId)->first();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
