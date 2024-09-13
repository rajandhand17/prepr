<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePathAchievement;
use App\Models\ChallengePathTemplateAchievement;
use Exception;

class ChallengePathTemplateAchievementsService
{
    public function addChallengePathTemplateAchievement($challengePathId, $templateChallengePathId)
    {
        try {
            $getChallengePathAchievement = ChallengePathAchievement::where('challenge_path_id', $challengePathId)->get();
            foreach ($getChallengePathAchievement as $challengePathTemplateAchievement) {
                $challengePathAchievement = new ChallengePathTemplateAchievement();
                $challengePathAchievement->challenge_path_template_id = $templateChallengePathId;
                $challengePathAchievement->achievement_name = $challengePathTemplateAchievement->achievement_name;
                $challengePathAchievement->achievement_points = $challengePathTemplateAchievement->achievement_points;
                $challengePathAchievement->achievement_image = $challengePathTemplateAchievement->achievement_image;
                $challengePathAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function redeemChallengePathTemplateToChallengePathAchievement($challengePathTemplateId, $redeemChallengePathId)
    {
        try {
            $getChallengePathTemplateAchievement = ChallengePathTemplateAchievement::where('challenge_path_template_id', $challengePathTemplateId)->first();
            if ($getChallengePathTemplateAchievement) {
                $newChallengePathAchievement = new ChallengePathAchievement();
                $newChallengePathAchievement->challenge_path_id = $redeemChallengePathId;
                $newChallengePathAchievement->achievement_name = $getChallengePathTemplateAchievement->achievement_name;
                $newChallengePathAchievement->achievement_points = $getChallengePathTemplateAchievement->achievement_points;
                $newChallengePathAchievement->achievement_image = $getChallengePathTemplateAchievement->getRawOriginal('achievement_image');
                $newChallengePathAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
