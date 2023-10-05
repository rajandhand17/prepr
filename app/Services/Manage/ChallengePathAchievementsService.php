<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengePathAchievement;
use Exception;

class ChallengePathAchievementsService
{
    public function uploadAchievementImage($achievementImage)
    {
        try {
            $upload_Achievement_image = FileUploadHelper::uploadImageToS3($achievementImage, 'achievement');
            if ($upload_Achievement_image == false) {
                return false;
            }

            return $upload_Achievement_image;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengePathAchievement($request, $challengePathId, $upload_achievements_image)
    {
        try {
            $challengePathAchievement = new ChallengePathAchievement();
            $challengePathAchievement->challenge_path_id = $challengePathId;
            $challengePathAchievement->achievement_name = $request->achievement_name;
            $challengePathAchievement->achievement_points = $request->achievement_points;
            $challengePathAchievement->achievement_image = $upload_achievements_image;
            $challengePathAchievement->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteChallengePathAchievement($challengePathId)
    {
        try {
            $challengePathAchievementCheck = ChallengePathAchievement::where('challenge_path_id', $challengePathId)->first();
            if ($challengePathAchievementCheck) {
                $challengePathAchievement = ChallengePathAchievement::where('challenge_path_id', $challengePathId)->delete();
                if (!$challengePathAchievement) {
                    return false;
                }

                return true;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
