<?php

namespace App\Services\Manage;

use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeTemplateSkillsGroupsStack;
use Exception;

class ChallengeTemplateSkillsGroupsStackService
{
    public function createChallengeTemplateSkills($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeSkillsGroupsStack = ChallengeSkillsGroupsStack::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeSkillsGroupsStack as $getSkillsGroupStack) {
                $ChallengeTemplateSkillsGroupsStack = new ChallengeTemplateSkillsGroupsStack();
                $ChallengeTemplateSkillsGroupsStack->template_challenge_id = $templateChallengeId;
                $ChallengeTemplateSkillsGroupsStack->foreign_id = $getSkillsGroupStack->foreign_id;
                $ChallengeTemplateSkillsGroupsStack->type = $getSkillsGroupStack->type;
                $ChallengeTemplateSkillsGroupsStack->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
