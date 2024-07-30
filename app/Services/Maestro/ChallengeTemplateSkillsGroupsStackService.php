<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeTemplateSkillsGroupsStack;
use Exception;

class ChallengeTemplateSkillsGroupsStackService
{
    public static function addChallengeTemplateSkills($challengeId, $templateChallengeId)
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
            return false;
        }
    }
}
