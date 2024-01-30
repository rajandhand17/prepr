<?php

namespace App\Services\Manage;

use App\Models\ChallengeAchievement;
use App\Models\TemplateChallengeAchievement;
use Exception;

class ChallengeTemplateAchievementService
{
    public function createChallengeTemplateAchievement($createChallengeId, $templateChallengeId)
    {
        try {
            $challengeParticipation = ChallengeAchievement::where('challenge_id', $createChallengeId)->get();
            foreach ($challengeParticipation as $challengeParticipationData) {
                $challengeParticipation = new TemplateChallengeAchievement();
                $challengeParticipation->template_challenge_id = $templateChallengeId;
                $challengeParticipation->achievement_type = $challengeParticipationData->achievement_type;
                $challengeParticipation->achievement_name = $challengeParticipationData->achievement_name;
                $challengeParticipation->achievement_prize = $challengeParticipationData->achievement_prize;
                $challengeParticipation->achievement_points = $challengeParticipationData->achievement_points;
                $challengeParticipation->achievement_image = $challengeParticipationData->achievement_image;
                $challengeParticipation->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
