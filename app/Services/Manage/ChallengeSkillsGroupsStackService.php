<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
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
                        if (is_array($skill) && isset($skill['key'])) {
                            $ChallengeSkillsGroupsStack->foreign_id = $skill['key'];
                        } elseif (is_numeric($skill)) {
                            $ChallengeSkillsGroupsStack->foreign_id = $skill;
                        }
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
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeSkills($originalChallengeSkills, $clonedChallengeId)
    {
        try {
            $originalChallengeSkills->each(function ($skills) use ($clonedChallengeId) {
                if ($skills) {
                    $cloneSkill = $skills->replicate();
                    $cloneSkill->challenge_id = $clonedChallengeId;
                    $cloneSkill->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeGroups($originalChallengeGroups, $clonedChallengeId)
    {
        try {
            $originalChallengeGroups->each(function ($skill_groups) use ($clonedChallengeId) {
                if ($skill_groups) {
                    $cloneSkillGroup = $skill_groups->replicate();
                    $cloneSkillGroup->challenge_id = $clonedChallengeId;
                    $cloneSkillGroup->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeStack($originalChallengeStacks, $clonedChallengeId)
    {
        try {
            $originalChallengeStacks->each(function ($skill_stacks) use ($clonedChallengeId) {
                if ($skill_stacks) {
                    $cloneSkillSTack = $skill_stacks->replicate();
                    $cloneSkillSTack->challenge_id = $clonedChallengeId;
                    $cloneSkillSTack->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getChallengeIdBasedOnSkills($skills)
    {
        try {
            $getChallengeIds = ChallengeSkillsGroupsStack::where('type', '0')
                ->whereIn('foreign_id', $skills)
                ->pluck('challenge_id');

            return $getChallengeIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getRecommendedSkills($getUserSkills)
    {
        try {
            $getChallengeSkillsIds = [];
            $challengeId = ChallengeSkillsGroupsStack::where('type', '0')
                ->whereIn('foreign_id', $getUserSkills)
                ->pluck('challenge_id');
            if (!empty($challengeId)) {
                $getChallengeSkillsIds = ChallengeSkillsGroupsStack::where('type', '0')
                    ->whereIn('challenge_id', $challengeId)
                    ->pluck('foreign_id')->unique();
            }

            return $getChallengeSkillsIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
