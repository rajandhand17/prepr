<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabSkillsGroupsStack;

class LabSkillsGroupsStackService
{
    public static function createLabSkillsGroupsStack($originalLabsSkills, $clonedLabId)
    {
        try {
            $originalLabsSkills->each(function ($skills) use ($clonedLabId) {
                if ($skills) {
                    $cloneSkill = $skills->replicate();
                    $cloneSkill->lab_id = $clonedLabId;
                    $cloneSkill->save();
                }
            });

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
