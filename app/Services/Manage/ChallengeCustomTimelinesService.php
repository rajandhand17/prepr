<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeCustomTimelines;
use Exception;

class ChallengeCustomTimelinesService
{
    public function createChallengeCustomTimelines($request, $challenge_id)
    {
        try {
            if ($request->timeline_type == 'flexible') {
                if ($request->custom_timelines_title !== null && $request->custom_timelines_date !== null) {
                    foreach ($request->custom_timelines_title as $key => $value) {
                        $custom_date = date('Y-m-d H:i:s', strtotime($request->custom_timelines_date[$key]));
                        $challengeCustomTimeline = new ChallengeCustomTimelines();
                        $challengeCustomTimeline->challenge_id = $challenge_id;
                        $challengeCustomTimeline->custom_timelines_title = $request->custom_timelines_title[$key];
                        $challengeCustomTimeline->custom_timelines_date = $custom_date;
                        $challengeCustomTimeline->custom_timelines_description = $request->custom_timelines_description[$key];
                        $challengeCustomTimeline->custom_timelines_duration = $request->custom_timelines_duration[$key];
                        $challengeCustomTimeline->schedule_custom_notify = $request->schedule_custom_notify[$key] ?? 0;
                        $challengeCustomTimeline->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateChallengeCustomTimelines($request, $challenge_id)
    {
        try {
            if ($request->has('timeline_type')) {
                if ($request->timeline_type == 'flexible') {
                    if ($request->custom_timelines_title !== null && $request->custom_timelines_date !== null) {
                        ChallengeCustomTimelines::where('challenge_id', $challenge_id)->delete();
                        foreach ($request->custom_timelines_title as $key => $value) {
                            $custom_date = date('Y-m-d H:i:s', strtotime($request->custom_timelines_date[$key]));
                            $challengeCustomTimeline = new ChallengeCustomTimelines();
                            $challengeCustomTimeline->challenge_id = $challenge_id;
                            $challengeCustomTimeline->custom_timelines_title = $request->custom_timelines_title[$key];
                            $challengeCustomTimeline->custom_timelines_date = $custom_date;
                            $challengeCustomTimeline->custom_timelines_description = $request->custom_timelines_description[$key];
                            $challengeCustomTimeline->custom_timelines_duration = $request->custom_timelines_duration[$key];
                            $challengeCustomTimeline->schedule_custom_notify = $request->schedule_custom_notify[$key] ?? 0;
                            $challengeCustomTimeline->save();
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

    public function cloneChallengeCustomTimelines($originalChallengeCustomTimelines, $clonedChallengeId)
    {
        try {
            $originalChallengeCustomTimelines->each(function ($challenge_custom_timelines) use ($clonedChallengeId) {
                if ($challenge_custom_timelines) {
                    $cloneAssessment = $challenge_custom_timelines->replicate();
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
