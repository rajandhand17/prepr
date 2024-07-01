<?php

namespace App\Services\Manage;

use App\Models\LabProgramsTagsGroups;

class LabProgramTagsGroupsService
{
    public function createLabProgramTagsGroups($request, $lab_program_id)
    {
        if ($request->has('tags')) {
            if (count($request->tags) > 0) {
                foreach ($request->tags as $tag) {
                    $labSkillsGroupsStack = new LabProgramsTagsGroups();
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
                    $LabSkillsGroupsStack = new LabProgramsTagsGroups();
                    $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                    $LabSkillsGroupsStack->foreign_id = $tag_group;
                    $LabSkillsGroupsStack->type = '1';
                    $LabSkillsGroupsStack->save();
                }
            }
        }

        return true;
    }

    public function updateLabProgramTagsGroups($request, $lab_program_id)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsLabTags = LabProgramsTagsGroups::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsLabTags, $request->tags);
                    $deleteNonExisting = LabProgramsTagsGroups::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTags = array_diff($request->tags, $getExistsLabTags);

                    foreach ($newTags as $tag) {
                        $LabSkillsGroupsStack = new LabProgramsTagsGroups();
                        $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                        $LabSkillsGroupsStack->foreign_id = $tag;
                        $LabSkillsGroupsStack->type = '0';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    $getExistsLabTagsGroups = LabProgramsTagsGroups::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsLabTagsGroups, $request->tag_groups);
                    $deleteNonExisting = LabProgramsTagsGroups::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tag_groups, $getExistsLabTagsGroups);
                    foreach ($newTagsGroups as $tag_group) {
                        $LabSkillsGroupsStack = new LabProgramsTagsGroups();
                        $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                        $LabSkillsGroupsStack->foreign_id = $tag_group;
                        $LabSkillsGroupsStack->type = '1';
                        $LabSkillsGroupsStack->save();
                    }
                }
            } else {
                LabProgramsTagsGroups::where([
                    ['lab_program_id', '=', $lab_program_id],
                    ['type', '=', '1'],
                ])->delete();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
