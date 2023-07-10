<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\SkillStack;

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
            return false;
        }
    }
}
