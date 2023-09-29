<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengeAchievement;
use Exception;

class ChallengeAchievementService
{
    public function uploadChallengeParticipationAchievementImage($image)
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
            $challengeAchievement->challenge_id = $challenge;
            $challengeAchievement->achievement_type = '0';
            $challengeAchievement->achievement_name = $request->achievement_name;
            $challengeAchievement->achievement_prize = $request->achievement_prize;
            $challengeAchievement->achievement_points = $request->achievement_points;
            $challengeAchievement->achievement_image = $upload_achievement_image;
            $challengeAchievement->save();

            if ($request->winner_achievement_participation !== null) {
                foreach ($request->winner_achievement_participation as $key => $value) {
                    $upload_incentive_achievement_image = FileUploadHelper::uploadImageToS3($request->winner_achievement_image[$key], 'achievement');
                    $challengeIncentiveAchievement = new ChallengeAchievement();
                    $challengeIncentiveAchievement->challenge_id = $challenge;
                    $challengeIncentiveAchievement->achievement_type = '1';
                    $challengeIncentiveAchievement->achievement_name = $request->winner_achievement_name[$key];
                    $challengeIncentiveAchievement->achievement_prize = $request->winner_achievement_prize[$key];
                    $challengeIncentiveAchievement->achievement_points = $request->winner_achievement_points[$key];
                    $challengeIncentiveAchievement->achievement_image = $upload_incentive_achievement_image;
                    $challengeIncentiveAchievement->save();
                }
            }

            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    public static function updateChallengeAchievement($challenge_id, $request, $update_participation_achievement_image)
    {
        try {
            $challengeAchievement = ChallengeAchievement::where('id', $challenge_id)->first();
            $challengeAchievement->achievement_type = '0';
            $challengeAchievement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $challengeAchievement->achievement_name;
            $challengeAchievement->achievement_prize = ($request->has('achievement_prize')) ? $request->achievement_prize : $challengeAchievement->achievement_prize;
            $challengeAchievement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $challengeAchievement->achievement_points;
            $challengeAchievement->achievement_image = ($update_participation_achievement_image) ? $update_participation_achievement_image : $challengeAchievement->achievement_image;
            $challengeAchievement->save();

            if (condition) {
                # code...
            }



        } catch (Exception $th) {
            return false;
        }
    }
}
