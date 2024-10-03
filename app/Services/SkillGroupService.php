<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Support\Facades\Schema;

class SkillGroupService
{
    public static function getSkillGroupsBasedOnIds($skill_group_ids)
    {
        try {
            $getSkillGroupList = SkillGroup::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title')
                ->whereIn('id', $skill_group_ids)->get();
            if ($getSkillGroupList) {
                return $getSkillGroupList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getSkillGroups($language = 'en', $search = null, $skill_stacks = null, $skills = null)
    {
        try {
            if ($language == 'en') {
                $skill_group = SkillGroup::select('id', 'title', 'skill_stacks', 'skills', 'description');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skill_groups', $column_name)) {
                    return false;
                }

                $skill_group = SkillGroup::select('id', $column_name.' as title', 'skill_stacks', 'skills', 'description');
            }

            //Search skill name based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $skill_group = $skill_group->where($column_name, 'like', '%'.$search.'%');
            }

            //Search skill stacks based on used input
            if ($skill_stacks != null) {
                $skill_group = $skill_group->where('skill_stacks', 'like', '%'.$skill_stacks.'%');
            }

            //Search skill based on used input
            if ($skills != null) {
                $skill_group = $skill_group->where('skills', 'like', '%'.$skills.'%');
            }

            //take 20 results based from the table
            $skill_group = $skill_group->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$skill_group->isEmpty()) {
                return $skill_group;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getSkillBasedOnSkillGroupsId($skill_group_ids)
    {
        try {
            $getSkills = SkillGroup::where('id', $skill_group_ids)->pluck('skills');
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
