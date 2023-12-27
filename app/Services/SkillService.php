<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\Skill;
use Illuminate\Support\Facades\Schema;

class SkillService
{
    public function getSkills($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $skill_list = Skill::select('id', 'title');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skills', $column_name)) {
                    return false;
                }
                $skill_list = Skill::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $skill_list = $this->filterSkillList($skill_list, $column_name, $search);
            }

            //take 20 results based from the table
            $skill_list = $skill_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$skill_list->isEmpty()) {
                return $skill_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterSKillList($getSkillsList, $sKill_column_name, $search)
    {
        try {
            $getSkillsList = $getSkillsList->where($sKill_column_name, 'like', '%'.$search.'%');
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSkillBasedOnIds($skill_ids)
    {
        try {
            $getSkillsList = Skill::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $skill_ids)->get();
            if ($getSkillsList) {
                return $getSkillsList;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSkillBasedOnSingleId($skill_ids)
    {
        try {
            $getSkillsList = Skill::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->where('id', $skill_ids)->get();
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
