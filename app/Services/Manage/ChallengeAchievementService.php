<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeAchievement;
use Exception;
use Illuminate\Support\Facades\Log;

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
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeAchievement($request, $challenge, $upload_achievement_image)
    {
        try {
            $challengeAchievement = new ChallengeAchievement();
            $challengeAchievement->challenge_id = $challenge;
            $challengeAchievement->achievement_type = '0';
            $challengeAchievement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : 'Participant';
            $challengeAchievement->achievement_prize = ($request->has('achievement_prize')) ? $request->achievement_prize : 'Points';
            $challengeAchievement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : 100;
            $challengeAchievement->achievement_image = $upload_achievement_image;
            $challengeAchievement->save();

            if ($request->winner_achievement_participation !== null) {
                foreach ($request->winner_achievement_participation as $key => $value) {
                    $upload_incentive_achievement_image = isset($request->winner_achievement_image[$key]) ? self::uploadChallengeIncentiveAchievementImage($request->winner_achievement_image[$key]) : config('site-settings.default_challenge_achievement_image');
                    $incentive_achievement_name = isset($request->winner_achievement_name[$key]) ? $request->winner_achievement_name[$key] : 'Incentive';
                    $incentive_achievement_prize = isset($request->achievement_prize[$key]) ? $request->achievement_prize[$key] : 'Points';
                    $incentive_achievement_points = isset($request->achievement_points[$key]) ? $request->achievement_points[$key] : 100;

                    $challengeIncentiveAchievement = new ChallengeAchievement();
                    $challengeIncentiveAchievement->challenge_id = $challenge;
                    $challengeIncentiveAchievement->achievement_type = '1';
                    $challengeIncentiveAchievement->achievement_name = $incentive_achievement_name;
                    $challengeIncentiveAchievement->achievement_prize = $incentive_achievement_prize;
                    $challengeIncentiveAchievement->achievement_points = $incentive_achievement_points;
                    $challengeIncentiveAchievement->achievement_image = $upload_incentive_achievement_image;
                    $challengeIncentiveAchievement->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeAchievement in ChallengeAchievementService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function uploadChallengeIncentiveAchievementImage($image)
    {
        try {
            $upload_incentive_image = FileUploadHelper::uploadImageToS3($image, 'achievement');
            if ($upload_incentive_image == false) {
                return false;
            }

            return $upload_incentive_image;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateChallengeAchievement($challenge_id, $request, $update_participation_achievement_image)
    {
        try {
            $challengeAchievement = ChallengeAchievement::where(['challenge_id' => $challenge_id, 'achievement_type' => '0'])->first();
            $challengeAchievement->achievement_type = '0';
            $challengeAchievement->achievement_name = ($request->has('achievement_name')) ? $request->achievement_name : $challengeAchievement->achievement_name;
            $challengeAchievement->achievement_prize = ($request->has('achievement_prize')) ? $request->achievement_prize : $challengeAchievement->achievement_prize;
            $challengeAchievement->achievement_points = ($request->has('achievement_points')) ? $request->achievement_points : $challengeAchievement->achievement_points;
            $challengeAchievement->achievement_image = ($update_participation_achievement_image) ? $update_participation_achievement_image : $challengeAchievement->achievement_image;
            $challengeAchievement->save();

            $challengeIncentiveData = !empty($request->winner_achievement_image) ? array_map(null, $request->winner_achievement_name, $request->winner_achievement_prize, $request->winner_achievement_points, $request->old_winner_achievement_image ?? [], $request->winner_achievement_image ?? []) : array_map(null, $request->winner_achievement_name, $request->winner_achievement_prize, $request->winner_achievement_points, $request->old_winner_achievement_image ?? []);
            if (!empty($challengeIncentiveData)) {
                $challengeIncentiveArrayData = [];
                foreach ($challengeIncentiveData as $key => $value) {
                    if (!empty($value[0]) && !empty($value[1]) && !empty($value[2])) {
                        $upload_incentive_achievement_image = config('site-settings.default_challenge_cover_image');
                        if (!empty($request->winner_achievement_image)) {
                            if (array_key_exists($key, $request->winner_achievement_image)) {
                                $upload_incentive_achievement_image = self::uploadChallengeIncentiveAchievementImage($request->winner_achievement_image[$key]);
                            } else {
                                $upload_incentive_achievement_image = str_replace(config('site-settings.aws_url'), '', $value[3]);
                            }
                        } elseif (!empty($value[3])) {
                            $upload_incentive_achievement_image = str_replace(config('site-settings.aws_url'), '', $value[3]);
                        }
                        $challengeIncentivesData['challenge_id'] = $challenge_id;
                        $challengeIncentivesData['achievement_type'] = '1';
                        $challengeIncentivesData['achievement_name'] = $value[0];
                        $challengeIncentivesData['achievement_prize'] = $value[1];
                        $challengeIncentivesData['achievement_points'] = $value[2];
                        $challengeIncentivesData['achievement_image'] = $upload_incentive_achievement_image;
                        $challengeIncentiveArrayData[] = $challengeIncentivesData;
                    }
                }
                if (!empty($challengeIncentiveArrayData)) {
                    ChallengeAchievement::where(['challenge_id' => $challenge_id, 'achievement_type' => '1'])->delete();
                    ChallengeAchievement::insert($challengeIncentiveArrayData);
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneChallengeParticipationAchievement($originalChallengeParticipationAchievement, $clonedChallengeId)
    {
        try {
            if ($originalChallengeParticipationAchievement) {
                $cloneParticipationAchievement = $originalChallengeParticipationAchievement->replicate();
                $cloneParticipationAchievement->challenge_id = $clonedChallengeId;
                $cloneParticipationAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneChallengeIncentiveAchievement($originalChallengeIncentiveAchievement, $clonedChallengeId)
    {
        try {
            $originalChallengeIncentiveAchievement->each(function ($incentive_achievement) use ($clonedChallengeId) {
                if ($incentive_achievement) {
                    $cloneIncentiveAchievement = $incentive_achievement->replicate();
                    $cloneIncentiveAchievement->challenge_id = $clonedChallengeId;
                    $cloneIncentiveAchievement->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengeAchievement($challengeId)
    {
        try {
            $challengeAchievement = ChallengeAchievement::where(['challenge_id' => $challengeId, 'achievement_type' => '0'])->first();

            return $challengeAchievement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
