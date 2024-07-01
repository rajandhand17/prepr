<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\AchievementConditionList;
use Illuminate\Support\Facades\Schema;

class AchievementConditionListService
{
    public function getAchievementConditionLists($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_status_list = AchievementConditionList::select('id', 'title');
            //Search categories based on user input
            } else {
                //get column title based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('achievement_condition_lists', $column_name)) {
                    return false;
                }
                $project_status_list = AchievementConditionList::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_status_list = $project_status_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_status_list = $project_status_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$project_status_list->isEmpty()) {
                return $project_status_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getAchievementConditionByID($language = 'en', $achievement_condition_id)
    {
        try {
            if ($language == 'en') {
                $achievement_condition = AchievementConditionList::select('id', 'title');
            } else {
                //get column title based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('achievement_condition_lists', $column_name)) {
                    return false;
                }
                $achievement_condition = AchievementConditionList::select('id', $column_name.' as title');
            }
            $achievement_condition = $achievement_condition->find($achievement_condition_id);

            return $achievement_condition;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
