<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Models\ChallengeAchievement;
use Exception;

class ChallengeAchievementService
{
    public static function challengeIncentives($request, $challenge)
    {
        try {
            $incentive_trophy = [];
            if ($request->file('incentive_trophy') && count($request->file('incentive_trophy')) > 0) {
                foreach ($request->incentive_trophy as $key => $image) {
                    $incentive_trophy[] = FileUploadHelper::uploadImageToS3($request->file('incentive_trophy')[$key], 'challenge_incentives');
                }
            }
            $incentiveMapping = array_map(null, $request->incentive_name, $request->incentive_prize, $request->incentive_point, $incentive_trophy);
            if (!empty($incentiveMapping)) {
                if (ChallengeAchievement::where('challenge_id', $challenge->id)->exists()) {
                    ChallengeAchievement::where('challenge_id', $challenge->id)->delete();
                }
                foreach ($incentiveMapping as $challengeIncentive) {
                    $incentive['challenge_id'] = $challenge->id;
                    $incentive['achievement_type'] = '1';
                    $incentive['achievement_name'] = $challengeIncentive[0];
                    $incentive['achievement_prize'] = $challengeIncentive[1];
                    $incentive['achievement_points'] = $challengeIncentive[2];
                    $incentive['achievement_image'] = !empty($challengeIncentive[3]) ? $challengeIncentive[3] : null;
                    ChallengeAchievement::create($incentive);
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeIncentives($challenge)
    {
        try {
            return ChallengeAchievement::where('challenge_id', $challenge->id)->get();
        } catch (Exception $e) {
            return false;
        }
    }
}
