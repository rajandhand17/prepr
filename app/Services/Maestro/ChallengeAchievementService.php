<?php

namespace App\Services\Maestro;

use App\Models\ChallengeAchievement;
use Exception;

class ChallengeAchievementService
{
    public static function challengeIncentives($request, $challenge)
    {
        try {
            if ($request->file('incentive_trophy') && count($request->file('incentive_trophy')) > 0) {
                foreach ($request->incentive_trophy as $key => $image) {
                    $filename = Str::random(10).'.'.$image->getClientOriginalExtension();
                    $images = Image::make($image)->resize(256, 256)->stream();
                    $img = Storage::disk('s3')->put('uploads/trophy/'.$filename, $images);
                    $incentive_trophy[] = 'uploads/trophy/'.$filename;
                }
            }

            for ($i = 0, $iMax = count($request->incentive_name); $i < $iMax; $i++) {
                $incentive['challenge_id'] = $challenge->id;
                $incentive['achievement_type'] = '1';
                $incentive['achievement_name'] = @$request->incentive_name[$i];
                $incentive['achievement_prize'] = @$request->incentive_prize[$i];
                $incentive['achievement_points'] = @$request->incentive_point[$i];
                if (isset($incentive_trophy[$i])) {
                    $incentive['achievement_image'] = $incentive_trophy[$i];
                } else {
                    $incentive['achievement_image'] = '';
                }
                ChallengeAchievement::create($incentive);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
