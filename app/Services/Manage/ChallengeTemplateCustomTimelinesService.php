<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeCustomTimelines;
use App\Models\ChallengeTemplateCustomTimeLine;
use Exception;

class ChallengeTemplateCustomTimelinesService
{
    public function addChallengeTemplateCustomTimeLines($challengeId, $templateChallengeId)
    {
        try {
            $challengeCustomTimelines = ChallengeCustomTimelines::where('challenge_id', $challengeId)->get();
            if ($challengeCustomTimelines) {
                foreach ($challengeCustomTimelines as $challengeCustomTimeline) {
                    $challengeTemplateCustomTimeLine = new ChallengeTemplateCustomTimeLine();
                    $challengeTemplateCustomTimeLine->challenge_template_id = $templateChallengeId;
                    $challengeTemplateCustomTimeLine->custom_timelines_title = $challengeCustomTimeline->custom_timelines_title;
                    $challengeTemplateCustomTimeLine->custom_timelines_date = $challengeCustomTimeline->custom_timelines_date;
                    $challengeTemplateCustomTimeLine->custom_timelines_description = $challengeCustomTimeline->custom_timelines_description;
                    $challengeTemplateCustomTimeLine->custom_timelines_duration = $challengeCustomTimeline->custom_timelines_duration;
                    $challengeTemplateCustomTimeLine->schedule_custom_notify = $challengeCustomTimeline->schedule_custom_notify;
                    $challengeTemplateCustomTimeLine->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemChallengeTemplateCustomTimelines($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateTimelines = ChallengeTemplateCustomTimeLine::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateTimelines)) {
                foreach ($checkChallengeTemplateTimelines as $challengeTimeline) {
                    $newChallengeCustomTimeline = new ChallengeCustomTimeLines();
                    $newChallengeCustomTimeline->challenge_id = $redeemChallengeId;
                    $newChallengeCustomTimeline->custom_timelines_title = $challengeTimeline->custom_timelines_title;
                    $newChallengeCustomTimeline->custom_timelines_date = $challengeTimeline->custom_timelines_date;
                    $newChallengeCustomTimeline->custom_timelines_description = $challengeTimeline->custom_timelines_description;
                    $newChallengeCustomTimeline->custom_timelines_duration = $challengeTimeline->custom_timelines_duration;
                    $newChallengeCustomTimeline->schedule_custom_notify = $challengeTimeline->schedule_custom_notify;
                    $newChallengeCustomTimeline->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeTemplateCustomTimelines($challengeTemplateId)
    {
        try {
            $challengeTemplateCustomTimelines = ChallengeTemplateCustomTimeline::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateCustomTimelines->isNotEmpty()) {
                $deleteChallengeTemplateCustomTimelines = ChallengeTemplateCustomTimeline::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateCustomTimelines) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
