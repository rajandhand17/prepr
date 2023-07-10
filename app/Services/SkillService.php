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
}
