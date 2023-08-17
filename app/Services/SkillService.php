<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\Skill;

class SkillService
{
    public function getSkillLists($request)
    {
        try {
            $getSkillsList = Skill::select('id', 'name', 'fr_CA_name');
            $getSkillsList = $this->filterSkillList($getSkillsList, $request);
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterSKillList($getSkillsList, $request)
    {
        try {
            if (isset($request->search) && !empty($request->search)) {
                $getSkillsList = $getSkillsList->where('name', 'like', '%'.$request->search.'%');
            }
            $getSkillsList = $getSkillsList->get();
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
                $skill_list = Skill::select('id', $column_name . ' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $skill_list = $skill_list->where($column_name, 'like', '%' . $search . '%');
            }

            //take 20 results based from the table
            $skill_list = $skill_list->take(20)->get();

            //check if there are any results
            if (!$skill_list->isEmpty()) {
                return $skill_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
