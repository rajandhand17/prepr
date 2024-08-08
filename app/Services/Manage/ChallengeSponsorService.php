<?php

namespace App\Services\Manage;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeSponsor;
use App\Models\Host;
use Exception;

class ChallengeSponsorService
{
    public function createChallengeSponsor($request, $challenge)
    {
        try {
            if ($request->host_id !== null) {
                foreach ($request->host_id as $key => $value) {
                    $challengeSponsor = new ChallengeSponsor();
                    $challengeSponsor->challenge_id = $challenge;
                    $challengeSponsor->host_id = $request->host_id[$key];
                    $challengeSponsor->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallengeSponsor($challenge_id, $request)
    {
        try {
            if ($request->has('host_id')) {
                if (count($request->host_id) > 0) {
                    $getExistsChallengeHostSponsor = ChallengeSponsor::where('challenge_id', $challenge_id)->get(['host_id']);
                    $existingHostSponsor = $getExistsChallengeHostSponsor->pluck('host_id')->all();
                    $nonExistingIds = array_diff($existingHostSponsor, $request->host_id);

                    ChallengeSponsor::where('challenge_id', $challenge_id)->whereIn('host_id', $nonExistingIds)->delete();
                    $newHostSponsor = array_diff($request->host_id, $existingHostSponsor);
                    foreach ($newHostSponsor as $hostSponsor) {
                        $challengeHostSponsor = new ChallengeSponsor();
                        $challengeHostSponsor->challenge_id = $challenge_id;
                        $challengeHostSponsor->host_id = $hostSponsor;
                        $challengeHostSponsor->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getHostBasedOnIds($host_ids)
    {
        try {
            $getHostsList = Host::select('id', LanguageColumnHelper::getLanguageColumnName(app()->getLocale(), 'title').' as title', 'link', 'image', 'status', 'created_at')
            ->whereIn('id', $host_ids)->get();
            if ($getHostsList) {
                return $getHostsList;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneChallengeSponsor($originalChallengeSponsors, $clonedChallengeId)
    {
        try {
            $originalChallengeSponsors->each(function ($hosts) use ($clonedChallengeId) {
                if ($hosts) {
                    $cloneChallengeSponsor = $hosts->replicate();
                    $cloneChallengeSponsor->challenge_id = $clonedChallengeId;
                    $cloneChallengeSponsor->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
