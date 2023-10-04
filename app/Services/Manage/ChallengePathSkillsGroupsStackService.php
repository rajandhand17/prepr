<?php

namespace App\Services\Manage;

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
            return false;
        }
    }
}
