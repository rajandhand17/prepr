<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Rank;
use Illuminate\Support\Facades\Schema;

class RankService
{
    public function getRanks($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $rank = Rank::select('id', 'title', 'description', 'image', 'category', 'point', 'no_of_use', 'status');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('ranks', $column_name)) {
                    return false;
                }
                $description = LanguageColumnHelper::getLanguageColumnName($language, 'description');

                $rank = Rank::select('id', $column_name.' as title', $description.' as description', 'image', 'category', 'point', 'no_of_use', 'status');
            }
            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $rank = $rank->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $rank = $rank->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$rank->isEmpty()) {
                return $rank;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
