<?php

namespace App\Services\Manage;

use App\Models\ChallengeTimelines;
use App\Models\TemplateChallengeTimeLine;
use Exception;

class ChallengeTemplateTimelinesService
{
    public function createChallengeTemplateTimelines($challengeId, $templateChallengeId)
    {
        try {
            $challengeTimelines = ChallengeTimelines::where('challenge_id', $challengeId)->get();
            if ($challengeTimelines) {
                foreach ($challengeTimelines as $challengeTimeline) {
                    $templateChallengeTimeline = new TemplateChallengeTimeLine();
                    $templateChallengeTimeline->Template_challenge_id = $templateChallengeId;
                    $templateChallengeTimeline->timeline_type = $challengeTimeline->timeline_type;
                    $templateChallengeTimeline->open_call_date = $challengeTimeline->open_call_date;
                    $templateChallengeTimeline->open_call_date_description = $challengeTimeline->open_call_date_description;
                    $templateChallengeTimeline->last_call_date = $challengeTimeline->last_call_date;
                    $templateChallengeTimeline->last_call_date_description = $challengeTimeline->last_call_date_description;
                    $templateChallengeTimeline->application_deadline_date = $challengeTimeline->application_deadline_date;
                    $templateChallengeTimeline->application_deadline_date_description = $challengeTimeline->application_deadline_date_description;
                    $templateChallengeTimeline->submission_deadline_date = $challengeTimeline->submission_deadline_date;
                    $templateChallengeTimeline->submission_deadline_date_description = $challengeTimeline->submission_deadline_date_description;
                    $templateChallengeTimeline->challenge_duration = $challengeTimeline->challenge_duration;
                    $templateChallengeTimeline->flexible_date_number = $challengeTimeline->flexible_date_number;
                    $templateChallengeTimeline->flexible_date_duration = $challengeTimeline->flexible_date_duration;
                    $templateChallengeTimeline->automatic_alert = $challengeTimeline->automatic_alert;
                    $templateChallengeTimeline->flexible_expire_deadline = $challengeTimeline->flexible_expire_deadline;
                    $templateChallengeTimeline->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
