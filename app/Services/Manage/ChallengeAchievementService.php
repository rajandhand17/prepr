<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengeAchievement;
use Exception;

class ChallengeAchievementService
{
    public function uploadChallengeAchievementImage($image)
    {
        try {
            $upload_achievement_image = FileUploadHelper::uploadImageToS3($image, 'achievement');
            if ($upload_achievement_image == false) {
                return false;
            }

            return $upload_achievement_image;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createChallengeAchievement($request, $challenge, $upload_achievement_image)
    {
        try {
            $challengeAchievement = new ChallengeAchievement();
            $challengeAchievement->challenge_id = $challenge->id;
            $challengeAchievement->achievement_type = "0";
            $challengeAchievement->achievement_name = $request->achievement_name;
            $challengeAchievement->achievement_prize = $request->achievement_prize;
            $challengeAchievement->achievement_points = $request->achievement_points;
            $challengeAchievement->achievement_image = $upload_achievement_image;
            $challengeAchievement->save();

            if ($request->winner_achievement_participation !== null) {
                foreach ($request->winner_achievement_participation as $key => $value) {
                    $challengeIncentiveAchievement = new ChallengeAchievement();
                    $challengeIncentiveAchievement->challenge_id = $challenge->id;
                    $challengeIncentiveAchievement->achievement_type = "1";
                    $challengeIncentiveAchievement->achievement_name = $request->winner_achievement_name[$key];
                    $challengeIncentiveAchievement->achievement_prize = $request->winner_achievement_prize[$key];
                    $challengeIncentiveAchievement->achievement_points = $request->winner_achievement_points[$key];
                    $challengeIncentiveAchievement->achievement_image = $upload_achievement_image;
                    $challengeIncentiveAchievement->save();
                }
            }
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
