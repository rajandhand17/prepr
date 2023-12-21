<?php

namespace App\Services\Manage;

use App\Models\ChallengeTemplateTimeLine;
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
                    $challengeTemplateTimeline = new TemplateChallengeTimeLine();
                    $challengeTemplateTimeline=new ChallengeTemplateTimeLine();
                    $challengeTemplateTimeline->Template_challenge_id = $templateChallengeId;
                    $challengeTemplateTimeline->timeline_type = $challengeTimeline->timeline_type;
                    $challengeTemplateTimeline->open_call_date = $challengeTimeline->open_call_date;
                    $challengeTemplateTimeline->open_call_date_description = $challengeTimeline->open_call_date_description;
                    $challengeTemplateTimeline->last_call_date = $challengeTimeline->last_call_date;
                    $challengeTemplateTimeline->last_call_date_description = $challengeTimeline->last_call_date_description;
                    $challengeTemplateTimeline->application_deadline_date = $challengeTimeline->application_deadline_date;
                    $challengeTemplateTimeline->application_deadline_date_description = $challengeTimeline->application_deadline_date_description;
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
            return false;
        }
    }
}
