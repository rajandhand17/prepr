<?php

namespace App\Services\Manage;

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
            return false;
        }
    }
}
