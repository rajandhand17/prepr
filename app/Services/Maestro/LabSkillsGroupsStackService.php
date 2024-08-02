<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabSkillsGroupsStack;

class LabSkillsGroupsStackService
{
    public static function createCloneLabSkillsGroupsStack($originalLabsSkills, $clonedLabId)
    {
        try {
            $originalLabsSkills->each(function ($skills) use ($clonedLabId) {
                if ($skills) {
                    $cloneSkill = $skills->replicate();
                    $cloneSkill->lab_id = $clonedLabId;
                    $cloneSkill->save();
                }
            });

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createLabSkillsGroupsStack($request, $lab_id)
    {
        try {
            if ($request->has('labSkills')) {
                if (count($request->labSkills) > 0) {
                    foreach ($request->labSkills as $skill) {
                        $LabSkillsGroupsStack = new LabSkillsGroupsStack();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
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
                        $LabSkillsGroupsStack->lab_id = $lab_id;
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
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->foreign_id = $skill_stack;
                        $LabSkillsGroupsStack->type = '2';
                        $LabSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function updateLabSkillsGroupsStack($request, $lab_id)
    {
        try {
            if ($request->has('labSkills')) {
                if (count($request->labSkills) > 0) {
                    $getExistsSkills = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->labSkills);
                    $deleteNonExistingSkills = LabSkillsGroupsStack::where([
                        ['lab_id', '=', $lab_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->labSkills, $getExistsSkills);
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteLabSkillsGroupsStack($lab_id)
    {
        try {
            if (LabSkillsGroupsStack::where('lab_id', $lab_id)->delete()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
