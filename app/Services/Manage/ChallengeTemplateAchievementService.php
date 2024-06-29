<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAchievement;
use App\Models\ChallengeTemplateAchievement;
use Exception;

class ChallengeTemplateAchievementService
{
    public function addChallengeTemplateAchievement($createChallengeId, $templateChallengeId)
    {
        try {
            $challengeParticipation = ChallengeAchievement::where('challenge_id', $createChallengeId)->get();
            foreach ($challengeParticipation as $challengeParticipationData) {
                $challengeParticipation = new ChallengeTemplateAchievement();
                $challengeParticipation->challenge_template_id = $templateChallengeId;
                $challengeParticipation->achievement_type = $challengeParticipationData->achievement_type;
                $challengeParticipation->achievement_name = $challengeParticipationData->achievement_name;
                $challengeParticipation->achievement_prize = $challengeParticipationData->achievement_prize;
                $challengeParticipation->achievement_points = $challengeParticipationData->achievement_points;
                $challengeParticipation->achievement_image = $challengeParticipationData->getRawOriginal('achievement_image');
                $challengeParticipation->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemChallengeTemplateAchievement($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $challengeTemplateDatas = ChallengeTemplateAchievement::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($challengeTemplateDatas)) {
                foreach ($challengeTemplateDatas as $challengeTemplate) {
                    $newChallengeAchievement = new ChallengeAchievement();
                    $newChallengeAchievement->challenge_id = $redeemChallengeId;
                    $newChallengeAchievement->achievement_type = $challengeTemplate->achievement_type;
                    $newChallengeAchievement->achievement_name = $challengeTemplate->achievement_name;
                    $newChallengeAchievement->achievement_prize = $challengeTemplate->achievement_prize;
                    $newChallengeAchievement->achievement_points = $challengeTemplate->achievement_points;
                    $newChallengeAchievement->achievement_image = $challengeTemplate->getRawOriginal('achievement_image');
                    $newChallengeAchievement->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeTemplateAchievement($challengeTemplateId)
    {
        try {
            $challengeTemplateAchievement = ChallengeTemplateAchievement::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateAchievement->isNotEmpty()) {
                $deleteChallengeTemplateAchievement = ChallengeTemplateAchievement::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateAchievement) {
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
