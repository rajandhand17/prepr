<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeExternalLink;
use Exception;

class ChallengeExternalLinkService
{
    public function createChallengeExternalLink($request, $challenge_id)
    {
        try {
            if ($request->has('external_links') && $request->get('external_link_ids')) {
                if (count($request->external_link_ids) > 0) {
                    foreach ($request->external_link_ids as $key => $value) {
                        if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                            $challengeExternalLink = new ChallengeExternalLink();
                            $challengeExternalLink->challenge_id = $challenge_id;
                            $challengeExternalLink->social_media_link = $request->external_links[$key];
                            $challengeExternalLink->social_link_id = $value;
                            $challengeExternalLink->save();
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateChallengeExternalLink($request, $challenge_id)
    {
        try {
            if ($request->has('external_links') && $request->get('external_link_ids')) {
                if (count($request->external_link_ids) > 0) {
                    $existExternalLinks = ChallengeExternalLink::where('challenge_id', $challenge_id)->pluck('social_link_id')->toArray();
                    $nonExistingIds = array_diff($existExternalLinks, $request->external_link_ids);
                    $deleteNonExistingExternalLinks = ChallengeExternalLink::where('challenge_id', $challenge_id)->whereIn('social_link_id', $nonExistingIds)->delete();
                    foreach ($request->external_link_ids as $key => $value) {
                        $challengeExternalLink = ChallengeExternalLink::select('id', 'social_media_link')->where([
                            ['challenge_id', '=', $challenge_id],
                            ['social_link_id', '=', $value],
                        ])->first();
                        if ($challengeExternalLink) {
                            if ($challengeExternalLink['social_media_link'] !== $request->external_links[$key]) {
                                $challengeExternalLink->social_media_link = $request->external_links[$key];
                                $challengeExternalLink->save();
                            }
                        }
                        if (!$challengeExternalLink) {
                            if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                                $challengeExternalLink = new ChallengeExternalLink();
                                $challengeExternalLink->challenge_id = $challenge_id;
                                $challengeExternalLink->social_media_link = $request->external_links[$key];
                                $challengeExternalLink->social_link_id = $value;
                                $challengeExternalLink->save();
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeExternalLink($challenge_id)
    {
        try {
            $checkExists = ChallengeExternalLink::select('id')->where('challenge_id', $challenge_id)->get()->toArray();
            if ($checkExists) {
                $deleteChallengeExternalLink = ChallengeExternalLink::whereIn('id', $checkExists)->delete();
                if (!$deleteChallengeExternalLink) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeExternalLink($originalChallengeExternalLink, $clonedChallengeId)
    {
        try {
            $originalChallengeExternalLink->each(function ($external_links) use ($clonedChallengeId) {
                if ($external_links) {
                    $cloneAssessment = $external_links->replicate();
                    $cloneAssessment->challenge_id = $clonedChallengeId;
                    $cloneAssessment->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
