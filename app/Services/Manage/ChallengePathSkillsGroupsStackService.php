<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePathSkillGroupStack;
use Exception;

class ChallengePathSkillsGroupsStackService
{
    public function createChallengePathSkillsGroupsStack($request, $challenge_path_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    foreach ($request->skills as $skill) {
                        $challengePathSkillsGroupsStack = new ChallengePathSkillGroupStack();
                        $challengePathSkillsGroupsStack->challenge_path_id = $challenge_path_id;
                        $challengePathSkillsGroupsStack->foreign_id = $skill;
                        $challengePathSkillsGroupsStack->type = '0';
                        $challengePathSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    foreach ($request->skill_groups as $skill_group) {
                        $challengePathSkillsGroupsStack = new ChallengePathSkillGroupStack();
                        $challengePathSkillsGroupsStack->challenge_path_id = $challenge_path_id;
                        $challengePathSkillsGroupsStack->foreign_id = $skill_group;
                        $challengePathSkillsGroupsStack->type = '1';
                        $challengePathSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    foreach ($request->skill_stacks as $skill_stack) {
                        $challengePathSkillsGroupsStack = new ChallengePathSkillGroupStack();
                        $challengePathSkillsGroupsStack->challenge_path_id = $challenge_path_id;
                        $challengePathSkillsGroupsStack->foreign_id = $skill_stack;
                        $challengePathSkillsGroupsStack->type = '2';
                        $challengePathSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateChallengePathSkillsGroupsStack($request, $challengePathId)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = ChallengePathSkillGroupStack::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = ChallengePathSkillGroupStack::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $challengePathSkills = new ChallengePathSkillGroupStack();
                        $challengePathSkills->challenge_path_id = $challengePathId;
                        $challengePathSkills->foreign_id = $skill;
                        $challengePathSkills->type = '0';
                        $challengePathSkills->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillsGroup = ChallengePathSkillGroupStack::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsSkillsGroup, $request->skill_groups);
                    $deleteNonExistingSkillsGroup = ChallengePathSkillGroupStack::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroup = array_diff($request->skill_groups, $getExistsSkillsGroup);
                    foreach ($newSkillGroup as $skill_group) {
                        $challengePathGroups = new ChallengePathSkillGroupStack();
                        $challengePathGroups->challenge_path_id = $challengePathId;
                        $challengePathGroups->foreign_id = $skill_group;
                        $challengePathGroups->type = '1';
                        $challengePathGroups->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStack = ChallengePathSkillGroupStack::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsSkillStack, $request->skill_stacks);
                    $deleteNonExistingSkillStack = ChallengePathSkillGroupStack::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillStack = array_diff($request->skill_stacks, $getExistsSkillStack);
                    foreach ($newSkillStack as $skill_stack) {
                        $challengePathStacks = new ChallengePathSkillGroupStack();
                        $challengePathStacks->challenge_path_id = $challengePathId;
                        $challengePathStacks->foreign_id = $skill_stack;
                        $challengePathStacks->type = '2';
                        $challengePathStacks->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengePathSkillGroupStack($challengePathId)
    {
        try {
            $challengePathIds = ChallengePathSkillGroupStack::where('challenge_path_id', $challengePathId)->pluck('id');
            if ($challengePathIds->isNotEmpty()) {
                $challengePathSkillGroupStack = ChallengePathSkillGroupStack::whereIn('id', $challengePathIds)->delete();
                if (!$challengePathSkillGroupStack) {
                    return false;
                }

                return true;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
