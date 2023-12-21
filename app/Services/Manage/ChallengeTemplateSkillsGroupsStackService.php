<?php

namespace App\Services\Manage;

use App\Models\ChallengeSkillsGroupsStack;
use App\Models\TemplateChallengeSkillsGroupsStack;
use Exception;

class ChallengeTemplateSkillsGroupsStackService
{
    public function createChallengeTemplateSkills($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeSkillsGroupsStack = ChallengeSkillsGroupsStack::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeSkillsGroupsStack as $getSkillsGroupStack) {
                $templateChallengeSkillsGroupsStack = new TemplateChallengeSkillsGroupsStack();
                $templateChallengeSkillsGroupsStack->template_challenge_id = $templateChallengeId;
                $templateChallengeSkillsGroupsStack->foreign_id = $getSkillsGroupStack->foreign_id;
                $templateChallengeSkillsGroupsStack->type = $getSkillsGroupStack->type;
                $templateChallengeSkillsGroupsStack->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
