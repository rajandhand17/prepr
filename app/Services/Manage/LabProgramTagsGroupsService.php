<?php

namespace App\Services\Manage;

use App\Models\LabProgramsTagsGroups;
use App\Models\LabTagsGroups;

class LabProgramTagsGroupsService
{
    public function createLabProgramTagsGroups($request, $lab_program_id)
    {

        if ($request->has('tags')) {
            if (count($request->tags) > 0) {
                foreach ($request->tags as $tag){
                    $labSkillsGroupsStack=new LabProgramsTagsGroups();
                    $labSkillsGroupsStack->lab_program_id = $lab_program_id;
                    $labSkillsGroupsStack->foreign_id = $tag;
                    $labSkillsGroupsStack->type = '0';
                    $labSkillsGroupsStack->save();
                }
            }
        }
        if ($request->has('tag_groups')) {
            if (count($request->tag_groups) > 0) {
                foreach ($request->tag_groups as $tag_group) {
                    $LabSkillsGroupsStack=new LabProgramsTagsGroups();
                    $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                    $LabSkillsGroupsStack->foreign_id = $tag_group;
                    $LabSkillsGroupsStack->type = '1';
                    $LabSkillsGroupsStack->save();
                }
            }
        }
        return true;
    }
}
