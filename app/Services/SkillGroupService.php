<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\SkillGroup;

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
            return false;
        }
    }
}
