<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeTemplateSkillsGroupsStack;
use Exception;

class ChallengeTemplateSkillsGroupsStackService
{
    public function addChallengeTemplateSkills($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeSkillsGroupsStack = ChallengeSkillsGroupsStack::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeSkillsGroupsStack as $getSkillsGroupStack) {
                $ChallengeTemplateSkillsGroupsStack = new ChallengeTemplateSkillsGroupsStack();
                $ChallengeTemplateSkillsGroupsStack->challenge_template_id = $templateChallengeId;
                $ChallengeTemplateSkillsGroupsStack->foreign_id = $getSkillsGroupStack->foreign_id;
                $ChallengeTemplateSkillsGroupsStack->type = $getSkillsGroupStack->type;
                $ChallengeTemplateSkillsGroupsStack->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemChallengeTemplateSkillGroupStack($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateSkillGroupStacks = ChallengeTemplateSkillsGroupsStack::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateSkillGroupStacks)) {
                foreach ($checkChallengeTemplateSkillGroupStacks as $challengeTemplateSkillGroupStack) {
                    $newChallengeSkillGroupStacks = new ChallengeSkillsGroupsStack();
                    $newChallengeSkillGroupStacks->challenge_id = $redeemChallengeId;
                    $newChallengeSkillGroupStacks->foreign_id = $challengeTemplateSkillGroupStack->foreign_id;
                    $newChallengeSkillGroupStacks->type = $challengeTemplateSkillGroupStack->type;
                    $newChallengeSkillGroupStacks->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeTemplateSkillsGroupsStacks($challengeTemplateId)
    {
        try {
            $challengeTemplateSkillsGroupsStacks = ChallengeTemplateSkillsGroupsStack::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateSkillsGroupsStacks->isNotEmpty()) {
                $deleteChallengeTemplateSkillsGroupsStacks = ChallengeTemplateSkillsGroupsStack::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateSkillsGroupsStacks) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
