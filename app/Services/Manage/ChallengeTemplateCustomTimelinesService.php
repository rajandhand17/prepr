<?php

namespace App\Services\Manage;

use App\Models\ChallengeCustomTimelines;
use App\Models\TemplateChallengeCustomeTimeLine;
use Exception;

class ChallengeTemplateCustomTimelinesService
{
    public function createChallengeTemplateCustomTimeLines($challengeId, $templateChallengeId)
    {
        try {
            $challengeCustomTimelines = ChallengeCustomTimelines::where('challenge_id', $challengeId)->get();
            if ($challengeCustomTimelines) {
                foreach ($challengeCustomTimelines as $challengeCustomTimeline) {
                    $templateChallengeCustomTimeLine = new TemplateChallengeCustomeTimeLine();
                    $templateChallengeCustomTimeLine->template_challenge_id = $templateChallengeId;
                    $templateChallengeCustomTimeLine->custom_timelines_title = $challengeCustomTimeline->custom_timelines_title;
                    $templateChallengeCustomTimeLine->custom_timelines_date = $challengeCustomTimeline->custom_timelines_date;
                    $templateChallengeCustomTimeLine->custom_timelines_description = $challengeCustomTimeline->custom_timelines_description;
                    $templateChallengeCustomTimeLine->custom_timelines_duration = $challengeCustomTimeline->custom_timelines_duration;
                    $templateChallengeCustomTimeLine->schedule_custom_notify = $challengeCustomTimeline->schedule_custom_notify;
                    $templateChallengeCustomTimeLine->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
