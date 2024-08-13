<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePathSkillGroupStack;
use App\Models\ChallengePathTemplateSkillsGroupsStack;
use Exception;

class ChallengePathTemplateSkillsGroupsStackService
{
    public static function addChallengePathTemplateSkillsGroupsStack($challengePathId, $templateChallengePathId)
    {
        try {
            $getChallengePathSkillsGroupsStack = ChallengePathSkillGroupStack::where('challenge_path_id', $challengePathId)->get();
            foreach ($getChallengePathSkillsGroupsStack as $getSkillsGroupStack) {
                $challengeTemplateSkillsGroupsStack = new ChallengePathTemplateSkillsGroupsStack();
                $challengeTemplateSkillsGroupsStack->challenge_path_template_id = $templateChallengePathId;
                $challengeTemplateSkillsGroupsStack->foreign_id = $getSkillsGroupStack->foreign_id;
                $challengeTemplateSkillsGroupsStack->type = $getSkillsGroupStack->type;
                $challengeTemplateSkillsGroupsStack->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
