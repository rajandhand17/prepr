<?php

namespace App\Services;

use App\Models\LabTagsGroups;

class LabTagsGroupsService
{
    public function createLabTagsGroups($request, $lab)
    {
        if ($request->has('tags')) {
            if (count($request->tags) > 0) {
                foreach ($request->tags as $tag) {
                    $LabSkillsGroupsStack = new LabTagsGroups();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->foreign_id = $tag;
                    $LabSkillsGroupsStack->type = '0';
                    $LabSkillsGroupsStack->save();
                }
            }
        }
        if ($request->has('tag_groups')) {
            if (count($request->tag_groups) > 0) {
                foreach ($request->tag_groups as $tag_group) {
                    $LabSkillsGroupsStack = new LabTagsGroups();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->foreign_id = $tag_group;
                    $LabSkillsGroupsStack->type = '1';
                    $LabSkillsGroupsStack->save();
                }
            }
        }
        return true;
    }
}
