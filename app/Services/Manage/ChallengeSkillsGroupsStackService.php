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
            dd($th);

            return false;
        }
    }
}
