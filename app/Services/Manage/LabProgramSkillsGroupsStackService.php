<?php

namespace App\Services\Manage;


use App\Models\LabProgramsSkillsGroupsStack;
use App\Models\LabSkillsGroupsStack;

class LabProgramSkillsGroupsStackService
{
    public function createLabProgramSkillsGroupsStack($request,$lab_program_id){

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
}
