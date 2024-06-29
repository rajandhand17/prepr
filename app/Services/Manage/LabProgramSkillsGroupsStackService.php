<?php

namespace App\Services\Manage;

use App\Models\LabProgramsSkillsGroupsStack;

class LabProgramSkillsGroupsStackService
{
    public function createLabProgramSkillsGroupsStack($request, $lab_program_id)
    {
        if ($request->has('skills')) {
            if (count($request->skills) > 0) {
                foreach ($request->skills as $skill) {
                    $LabSkillsGroupsStack = new LabProgramsSkillsGroupsStack();
                    $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                    $LabSkillsGroupsStack->foreign_id = $skill;
                    $LabSkillsGroupsStack->type = '0';
                    $LabSkillsGroupsStack->save();
                }
            }
        }
        if ($request->has('skill_groups')) {
            if (count($request->skill_groups) > 0) {
                foreach ($request->skill_groups as $skill_group) {
                    $LabSkillsGroupsStack = new LabProgramsSkillsGroupsStack();
                    $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                    $LabSkillsGroupsStack->foreign_id = $skill_group;
                    $LabSkillsGroupsStack->type = '1';
                    $LabSkillsGroupsStack->save();
                }
            }
        }
        if ($request->has('skill_stacks')) {
            if (count($request->skill_stacks) > 0) {
                foreach ($request->skill_stacks as $skill_stack) {
                    $LabSkillsGroupsStack = new LabProgramsSkillsGroupsStack();
                    $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                    $LabSkillsGroupsStack->foreign_id = $skill_stack;
                    $LabSkillsGroupsStack->type = '2';
                    $LabSkillsGroupsStack->save();
                }
            }
        }

        return true;
    }

    public function updateLabProgramSkillsGroupsStack($request, $lab_program_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = LabProgramsSkillsGroupsStack::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = LabProgramsSkillsGroupsStack::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $LabSkillsGroupsStack = new LabProgramsSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                        $LabSkillsGroupsStack->foreign_id = $skill;
                        $LabSkillsGroupsStack->type = '0';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillsGroup = LabProgramsSkillsGroupsStack::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsSkillsGroup, $request->skill_groups);
                    $deleteNonExistingSkillsGroup = LabProgramsSkillsGroupsStack::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroup = array_diff($request->skill_groups, $getExistsSkillsGroup);
                    foreach ($newSkillGroup as $skill_group) {
                        $LabSkillsGroupsStack = new LabProgramsSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                        $LabSkillsGroupsStack->foreign_id = $skill_group;
                        $LabSkillsGroupsStack->type = '1';
                        $LabSkillsGroupsStack->save();
                    }
                }
            } else {
                $deleteNonExistingSkillsGroup = LabProgramsSkillsGroupsStack::where([
                    ['lab_program_id', '=', $lab_program_id],
                    ['type', '=', '1'],
                ])->delete();
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStack = LabProgramsSkillsGroupsStack::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsSkillStack, $request->skill_stacks);
                    $deleteNonExistingSkillStack = LabProgramsSkillsGroupsStack::where([
                        ['lab_program_id', '=', $lab_program_id],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillStack = array_diff($request->skill_stacks, $getExistsSkillStack);
                    foreach ($newSkillStack as $skill_stack) {
                        $LabSkillsGroupsStack = new LabProgramsSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_program_id = $lab_program_id;
                        $LabSkillsGroupsStack->foreign_id = $skill_stack;
                        $LabSkillsGroupsStack->type = '2';
                        $LabSkillsGroupsStack->save();
                    }
                }
            } else {
                $deleteNonExistingSkillStack = LabProgramsSkillsGroupsStack::where([
                    ['lab_program_id', '=', $lab_program_id],
                    ['type', '=', '2'],
                ])->delete();
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
