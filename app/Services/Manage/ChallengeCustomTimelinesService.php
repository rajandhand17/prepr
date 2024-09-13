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
                if ($request->custom_timelines_title != null && $request->custom_timelines_number != null) {
                    foreach ($request->custom_timelines_title as $key => $value) {
                        switch ($request->schedule_custom_notify[$key]) {
                            case 'no':
                                $schedule_custom_notify = '0';
                                break;
                            case 'yes':
                                $schedule_custom_notify = '1';
                                break;
                            default:
                                $schedule_custom_notify = '0';
                                break;
                        }
                        $challengeCustomTimeline = new ChallengeCustomTimelines();
                        $challengeCustomTimeline->challenge_id = $challenge_id;
                        $challengeCustomTimeline->custom_timelines_title = $request->custom_timelines_title[$key];
                        $challengeCustomTimeline->custom_timelines_number = $request->custom_timelines_number[$key] ?? 2;
                        $challengeCustomTimeline->custom_timelines_description = $request->custom_timelines_description[$key];
                        $challengeCustomTimeline->custom_timelines_duration = $request->custom_timelines_duration[$key] ?? 'weeks';
                        $challengeCustomTimeline->schedule_custom_notify = $schedule_custom_notify;
                        $challengeCustomTimeline->save();

                        if ($schedule_custom_notify == '1') {
                            $storeChallengeFlexibleAnnouncement = ChallengeFlexibleAnnouncementService::storeChallengeFlexibleAnnouncement($request, $challenge_id, $challengeCustomTimeline->id);
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

    public function updateChallengeCustomTimelines($request, $challenge_id)
    {
        try {
            if ($request->timeline_type == 'flexible') {
                if ($request->custom_timelines_title != null && $request->custom_timelines_number != null) {
                    ChallengeCustomTimelines::where('challenge_id', $challenge_id)->delete();
                    foreach ($request->custom_timelines_title as $key => $value) {
                        switch ($request->schedule_custom_notify[$key]) {
                            case 'no':
                                $schedule_custom_notify = '0';
                                break;
                            case 'yes':
                                $schedule_custom_notify = '1';
                                break;
                            default:
                                $schedule_custom_notify = '0';
                                break;
                        }
                        $challengeCustomTimeline = new ChallengeCustomTimelines();
                        $challengeCustomTimeline->challenge_id = $challenge_id;
                        $challengeCustomTimeline->custom_timelines_title = $request->custom_timelines_title[$key];
                        $challengeCustomTimeline->custom_timelines_number = $request->custom_timelines_number[$key] ?? 2;
                        $challengeCustomTimeline->custom_timelines_description = $request->custom_timelines_description[$key];
                        $challengeCustomTimeline->custom_timelines_duration = $request->custom_timelines_duration[$key] ?? 'weeks';
                        $challengeCustomTimeline->schedule_custom_notify = $schedule_custom_notify;
                        $challengeCustomTimeline->save();

                        if ($schedule_custom_notify == '1') {
                            $storeChallengeFlexibleAnnouncement = ChallengeFlexibleAnnouncementService::storeChallengeFlexibleAnnouncement($request, $challenge_id, $challengeCustomTimeline->id);
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
