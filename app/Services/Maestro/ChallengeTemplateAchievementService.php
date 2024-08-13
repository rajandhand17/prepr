<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAchievement;
use App\Models\ChallengeTemplateAchievement;
use Exception;

class ChallengeTemplateAchievementService
{
    public static function addChallengeTemplateAchievement($createChallengeId, $templateChallengeId)
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
}
