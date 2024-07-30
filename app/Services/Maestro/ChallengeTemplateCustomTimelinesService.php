<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeCustomTimelines;
use App\Models\ChallengeTemplateCustomTimeLine;
use Exception;

class ChallengeTemplateCustomTimelinesService
{
    public static function addChallengeTemplateCustomTimeLines($challengeId, $templateChallengeId)
    {
        try {
            $challengeCustomTimelines = ChallengeCustomTimelines::where('challenge_id', $challengeId)->get();
            if ($challengeCustomTimelines) {
                foreach ($challengeCustomTimelines as $challengeCustomTimeline) {
                    $challengeTemplateCustomTimeLine = new ChallengeTemplateCustomTimeLine();
                    $challengeTemplateCustomTimeLine->challenge_template_id = $templateChallengeId;
                    $challengeTemplateCustomTimeLine->custom_timelines_title = $challengeCustomTimeline->custom_timelines_title;
                    $challengeTemplateCustomTimeLine->custom_timelines_number = $challengeCustomTimeline->custom_timelines_number;
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
    }
