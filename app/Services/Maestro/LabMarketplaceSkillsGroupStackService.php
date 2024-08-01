<?php

namespace App\Services\Maestro;

use App\Models\LabMarketplaceSkillsGroupsStack;
use App\Models\LabSkillsGroupsStack;
use Exception;

class LabMarketplaceSkillsGroupStackService
{
    public static function addLabMarketplaceSkillsGroupsStack($labMarketplaceId, $labId)
    {
        try {
            $exisingLabSkillsGroupsStack = LabSkillsGroupsStack::where('lab_id', $labId)->get();
            if ($exisingLabSkillsGroupsStack) {
                foreach ($exisingLabSkillsGroupsStack as $skillsGroup) {
                    $labTemplateSkillsGroupStack = new LabMarketplaceSkillsGroupsStack();
                    $labTemplateSkillsGroupStack->lab_marketplace_id = $labMarketplaceId;
                    $labTemplateSkillsGroupStack->foreign_id = $skillsGroup->foreign_id;
                    $labTemplateSkillsGroupStack->type = $skillsGroup->type;
                    $labTemplateSkillsGroupStack->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
