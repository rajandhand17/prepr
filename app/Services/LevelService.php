<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Levels;
use Illuminate\Support\Facades\Schema;

class LevelService
{
    public function getLevels($language, $search)
    {
        try {
            if ($language == 'en') {
                $levels = Levels::select('id', 'title');
            } else {
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not

                if (!$column_name || !Schema::hasColumn('durations', $column_name)) {
                    return false;
                }
                $levels = Levels::select('id', $column_name.' as title');
            }
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $levels = $levels->where($column_name, 'like', '%'.$search.'%');
            }
            $levels = $levels->take(config('site-settings.dropdown_listing_limit'))->get();
            if (!$levels->isEmpty()) {
                return $levels;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLevelsBasedOnId($levelId)
    {
        try {
            return Levels::find($levelId)->first();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
