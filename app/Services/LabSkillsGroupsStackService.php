<?php

namespace App\Services;

use App\Models\LabSkillsGroupsStack;

class LabSkillsGroupsStackService
{
    public function createLabSkillsGroupsStack($request, $lab)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    foreach ($request->skills as $skill) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab->id;
                        $LabSkillsGroupsStack->foreign_id = $skill;
                        $LabSkillsGroupsStack->type = '0';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    foreach ($request->skill_groups as $skill_group) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab->id;
                        $LabSkillsGroupsStack->foreign_id = $skill_group;
                        $LabSkillsGroupsStack->type = '1';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    foreach ($request->skill_stacks as $skill_stack) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab->id;
                        $LabSkillsGroupsStack->foreign_id = $skill_stack;
                        $LabSkillsGroupsStack->type = '2';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function deleteLabSkillsGroupsStack($lab_id){
        try {
            $checkExistsLabSkillsGroupsStack=LabSkillsGroupsStack::select('id')->where("lab_id",$lab_id)->get()->toArray();
            if($checkExistsLabSkillsGroupsStack){
                $deleteLabSkillsGroupsStack=LabSkillsGroupsStack::whereIn('id',$checkExistsLabSkillsGroupsStack)->delete();
                if(!$deleteLabSkillsGroupsStack){
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
        return false;
        }
    }
}
