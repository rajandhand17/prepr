<?php

namespace App\Services\Manage;

use App\Models\ChallengeSkillsGroupsStack;
use Exception;

class ChallengeSkillsGroupsStackService
{
    public function createChallengeSkillsGroupsStack($request, $challenge)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    foreach ($request->skills as $skill) {
                        $ChallengeSkillsGroupsStack = new ChallengeSkillsGroupsStack();
                        $ChallengeSkillsGroupsStack->challenge_id = $challenge;
                        $ChallengeSkillsGroupsStack->foreign_id = $skill;
                        $ChallengeSkillsGroupsStack->type = '0';
                        $ChallengeSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    foreach ($request->skill_groups as $skill_group) {
                        $ChallengeSkillsGroupsStack = new ChallengeSkillsGroupsStack();
                        $ChallengeSkillsGroupsStack->challenge_id = $challenge;
                        $ChallengeSkillsGroupsStack->foreign_id = $skill_group;
                        $ChallengeSkillsGroupsStack->type = '1';
                        $ChallengeSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    foreach ($request->skill_stacks as $skill_stack) {
                        $ChallengeSkillsGroupsStack = new ChallengeSkillsGroupsStack();
                        $ChallengeSkillsGroupsStack->challenge_id = $challenge;
                        $ChallengeSkillsGroupsStack->foreign_id = $skill_stack;
                        $ChallengeSkillsGroupsStack->type = '2';
                        $ChallengeSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    public function updateChallengeSkillsGroupsStack($request, $challenge_id)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = ChallengeSkillsGroupsStack::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = ChallengeSkillsGroupsStack::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $challengeSkillsGroupsStack = new ChallengeSkillsGroupsStack();
                        $challengeSkillsGroupsStack->challenge_id = $challenge_id;
                        $challengeSkillsGroupsStack->foreign_id = $skill;
                        $challengeSkillsGroupsStack->type = '0';
                        $challengeSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_groups')) {
                if (count($request->skill_groups) > 0) {
                    $getExistsSkillsGroup = ChallengeSkillsGroupsStack::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillsGroup, $request->skill_groups);
                    $deleteNonExistingSkillsGroup = ChallengeSkillsGroupsStack::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillGroup = array_diff($request->skill_groups, $getExistsSkillsGroup);
                    foreach ($newSkillGroup as $skill_group) {
                        $challengeSkillsGroupsStack = new ChallengeSkillsGroupsStack();
                        $challengeSkillsGroupsStack->challenge_id = $challenge_id;
                        $challengeSkillsGroupsStack->foreign_id = $skill_group;
                        $challengeSkillsGroupsStack->type = '1';
                        $challengeSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('skill_stacks')) {
                if (count($request->skill_stacks) > 0) {
                    $getExistsSkillStack = ChallengeSkillsGroupsStack::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '2'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkillStack, $request->skill_stacks);
                    $deleteNonExistingSkillStack = ChallengeSkillsGroupsStack::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '2'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newSkillStack = array_diff($request->skill_stacks, $getExistsSkillStack);
                    foreach ($newSkillStack as $skill_stack) {
                        $challengeSkillsGroupsStack = new ChallengeSkillsGroupsStack();
                        $challengeSkillsGroupsStack->challenge_id = $challenge_id;
                        $challengeSkillsGroupsStack->foreign_id = $skill_stack;
                        $challengeSkillsGroupsStack->type = '2';
                        $challengeSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
