<?php

namespace App\Services\Manage;

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
                        if (is_array($skill) && isset($skill['key'])) {
                            $LabSkillsGroupsStack->foreign_id = $skill['key'];
                        } elseif (is_numeric($skill)) {
                            $LabSkillsGroupsStack->foreign_id = $skill;
                        }
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

    public function updateLabSkillsGroupsStack($request, $lab_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $skill;
                        $LabSkillsGroupsStack->type = '0';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillsGroup = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillsGroup, $request->skill_groups);
                    $deleteNonExistingSkillsGroup = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroup = array_diff($request->skill_groups, $getExistsSkillsGroup);
                    foreach ($newSkillGroup as $skill_group) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $skill_group;
                        $LabSkillsGroupsStack->type = '1';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStack = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillStack, $request->skill_stacks);
                    $deleteNonExistingSkillStack = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillStack = array_diff($request->skill_stacks, $getExistsSkillStack);
                    foreach ($newSkillStack as $skill_stack) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
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

    public static function deleteLabSkillsGroupsStack($lab_id)
    {
        try {
            $checkExistsLabSkillsGroupsStack = LabSkillsGroupsStack::select('id')->where('lab_id', $lab_id)->get()->toArray();
            if ($checkExistsLabSkillsGroupsStack) {
                $deleteLabSkillsGroupsStack = LabSkillsGroupsStack::whereIn('id', $checkExistsLabSkillsGroupsStack)->delete();
                if (!$deleteLabSkillsGroupsStack) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabIdBasesOnSKillsId($usersSkills)
    {
        try {
            if (count($usersSkills) > 0) {
                $getSkills = LabSkillsGroupsStack::where('type', 0)
                    ->whereIn('foreign_id', $usersSkills)
                    ->pluck('foreign_id');
            } else {
                $getSkills = LabSkillsGroupsStack::where('type', 0)
                    ->pluck('foreign_id')->random();
            }

            return $getSkills;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabIdBasedOnSkill($id)
    {
        try {
            $getLabId = LabSkillsGroupsStack::where('type', '0')
                ->where('foreign_id', $id)
                ->pluck('lab_id');

            return $getLabId;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSkillsBasedOnLabId($labId)
    {
        try {
            $getLabId = LabSkillsGroupsStack::where('type', '0')
                ->where('lab_id', $labId)
                ->pluck('foreign_id');

            return $getLabId;
        } catch (\Exception $e) {
            return false;
        }
    }
}
