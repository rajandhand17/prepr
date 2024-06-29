<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTemplateSkillsGroupsStack;

class labMarketplaceTagsGroupsService
{
    public function createLabWorkplaceSkillsGroupsStack($labTemplateId, $lab)
    {
        try {
            $exisingLabSkillsGroupsStack = LabSkillsGroupsStack::where('lab_id', $lab->id)->get();
            if ($exisingLabSkillsGroupsStack) {
                foreach ($exisingLabSkillsGroupsStack as $skillsGroup) {
                    $labTemplateSkillsGroupStack = new LabTemplateSkillsGroupsStack();
                    $labTemplateSkillsGroupStack->template_lab_id = $labTemplateId->id;
                    $labTemplateSkillsGroupStack->foreign_id = $skillsGroup->foreign_id;
                    $labTemplateSkillsGroupStack->type = $skillsGroup->type;
                    $labTemplateSkillsGroupStack->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
