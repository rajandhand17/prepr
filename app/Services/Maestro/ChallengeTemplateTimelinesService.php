<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTemplateTimeLine;
use App\Models\ChallengeTimelines;
use Exception;

class ChallengeTemplateTimelinesService
{
    public static function addChallengeTemplateTimelines($challengeId, $templateChallengeId)
    {
        try {
            $challengeTimelines = ChallengeTimelines::where('challenge_id', $challengeId)->get();
            if ($challengeTimelines) {
                foreach ($challengeTimelines as $challengeTimeline) {
                    $challengeTemplateTimeline = new ChallengeTemplateTimeLine();
                    $challengeTemplateTimeline->challenge_template_id = $templateChallengeId;
                    $challengeTemplateTimeline->timeline_type = $challengeTimeline->timeline_type;
                    $challengeTemplateTimeline->start_date = $challengeTimeline->start_date;
                    $challengeTemplateTimeline->start_date_description = $challengeTimeline->start_date_description;
                    $challengeTemplateTimeline->registration_deadline_date = $challengeTimeline->registration_deadline_date;
                    $challengeTemplateTimeline->registration_deadline_date_description = $challengeTimeline->registration_deadline_date_description;
                    $challengeTemplateTimeline->submission_deadline_date = $challengeTimeline->submission_deadline_date;
                    $challengeTemplateTimeline->submission_deadline_date_description = $challengeTimeline->submission_deadline_date_description;
                    $challengeTemplateTimeline->challenge_duration = $challengeTimeline->challenge_duration;
                    $challengeTemplateTimeline->flexible_date_number = $challengeTimeline->flexible_date_number;
                    $challengeTemplateTimeline->flexible_date_duration = $challengeTimeline->flexible_date_duration;
                    $challengeTemplateTimeline->automatic_alert = $challengeTimeline->automatic_alert;
                    $challengeTemplateTimeline->flexible_expire_deadline = $challengeTimeline->flexible_expire_deadline;
                    $challengeTemplateTimeline->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
