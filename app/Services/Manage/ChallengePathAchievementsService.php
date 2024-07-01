<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallengePathAchievement($request, $challengePathId, $upload_achievement_image)
    {
        try {
            $checkExistsChallengePathAchievement = ChallengePathAchievement::where('challenge_path_id', $challengePathId)->first();
            if (!$checkExistsChallengePathAchievement) {
                $challengePathAchievement = new ChallengePathAchievement();
                $challengePathAchievement->challenge_path_id = $challengePathId;
                $challengePathAchievement->achievement_name = $request->achievement_name;
                $challengePathAchievement->achievement_points = $request->achievement_points;
                $challengePathAchievement->achievement_image = $upload_achievement_image;
                $challengePathAchievement->save();

                return true;
            }
            $checkExistsChallengePathAchievement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $checkExistsChallengePathAchievement->achievement_name;
            $checkExistsChallengePathAchievement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $checkExistsChallengePathAchievement->achievement_points;
            $checkExistsChallengePathAchievement->achievement_image = ($upload_achievement_image) ? $upload_achievement_image : $checkExistsChallengePathAchievement->achievement_image;
            $checkExistsChallengePathAchievement->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }
}
