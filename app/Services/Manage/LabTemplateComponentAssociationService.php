<?php

namespace App\Services\Manage;

use App\Models\ComponentAssociation;
use App\Models\TemplateComponentAssociation;

class LabTemplateComponentAssociationService
{
    public function createLabTemplateAssociation($labTemplateId, $lab)
    {
        try {
            $componentAssociation = ComponentAssociation::where('lab_id', $lab->id)->get();
            if ($componentAssociation) {
                foreach ($componentAssociation as $association) {
                    if($association->lab_program_id !==""){

                    }
                    $labSkillsGroupsStack = new TemplateComponentAssociation();
                    $labSkillsGroupsStack->template_lab_id = $labTemplateId->id;
                    $labSkillsGroupsStack->template_lab_program_id = $association->lab_program_id;
                    $labSkillsGroupsStack->template_challenge_id = $association->challenge_id;
                    $labSkillsGroupsStack->template_challenge_path_id = $association->challenge_path_id;
                    $labSkillsGroupsStack->template_resource_module_id = $association->resource_module_id;
                    $labSkillsGroupsStack->template_resource_collection_id = $association->resource_collection_id;
                    $labSkillsGroupsStack->template_resource_group_id = $association->resource_group_id;
                    $labSkillsGroupsStack->sequence = $association->sequence;
                    $labSkillsGroupsStack->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
