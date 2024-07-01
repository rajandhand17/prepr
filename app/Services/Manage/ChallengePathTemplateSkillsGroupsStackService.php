<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePathSkillGroupStack;
use App\Models\ChallengePathTemplateSkillsGroupsStack;
use Exception;

class ChallengePathTemplateSkillsGroupsStackService
{
    public function addChallengePathTemplateSkillsGroupsStack($challengePathId, $templateChallengePathId)
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

    public function redeemChallengePathTemplateToChallengePathSkillsGroupsStack($challengePathTemplateId, $redeemChallengePathId)
    {
        try {
            $getChallengePathTemplateSkillsGroupsStack = ChallengePathTemplateSkillsGroupsStack::where('challenge_path_template_id', $challengePathTemplateId)->get();
            if ($getChallengePathTemplateSkillsGroupsStack->isNotEmpty()) {
                $newChallengePathSkillsGroupsStack = new ChallengePathSkillGroupStack();
                $newChallengePathSkillsGroupsStack->challenge_path_id = $redeemChallengePathId;
                $newChallengePathSkillsGroupsStack->foreign_id = $getChallengePathTemplateSkillsGroupsStack->foreign_id;
                $newChallengePathSkillsGroupsStack->type = $getChallengePathTemplateSkillsGroupsStack->type;
                $newChallengePathSkillsGroupsStack->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
