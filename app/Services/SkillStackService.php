<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\SkillStack;
use Illuminate\Support\Facades\Schema;

class SkillStackService
{
    public static function getSkillStacksBasedOnIds($skill_stack_ids)
    {
        try {
            $getSkillStackList = SkillStack::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $skill_stack_ids)->get();
            if ($getSkillStackList) {
                return $getSkillStackList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getSkillStacks($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $skill_stacks = SkillStack::select('id', 'title', 'skills', 'description');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skill_stacks', $column_name)) {
                    return false;
                }
                $description = LanguageColumnHelper::getLanguageColumnName($language, 'description');
                $skill_stacks = SkillStack::select('id', $column_name.' as title', 'skills', $description.' as description');
            }

            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $skill_stacks = $skill_stacks->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $skill_stacks = $skill_stacks->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$skill_stacks->isEmpty()) {
                return $skill_stacks;
            }

            return false;
        } catch (\Exception) {
            return false;
        }
    }

    public static function getSkillBasedOnSkillStacksId($skill_set_id)
    {
        try {
            $getSkills = SkillStack::where('id', $skill_set_id)->pluck('skills');
            if ($getSkills) {
                return json_decode($getSkills);
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
