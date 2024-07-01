<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTemplateTimeLine;
use App\Models\ChallengeTimelines;
use Exception;

class ChallengeTemplateTimelinesService
{
    public function addChallengeTemplateTimelines($challengeId, $templateChallengeId)
    {
        try {
            $challengeTimelines = ChallengeTimelines::where('challenge_id', $challengeId)->get();
            if ($challengeTimelines) {
                foreach ($challengeTimelines as $challengeTimeline) {
                    $challengeTemplateTimeline = new ChallengeTemplateTimeLine();
                    $challengeTemplateTimeline->challenge_template_id = $templateChallengeId;
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function redeemChallengeTemplateTimeline($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateTimelines = ChallengeTemplateTimeLine::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateTimelines)) {
                foreach ($checkChallengeTemplateTimelines as $challengeTimeline) {
                    $newChallengeTimeline = new ChallengeTimelines();
                    $newChallengeTimeline->challenge_id = $redeemChallengeId;
                    $newChallengeTimeline->timeline_type = $challengeTimeline->timeline_type;
                    $newChallengeTimeline->open_call_date = $challengeTimeline->open_call_date;
                    $newChallengeTimeline->open_call_date_description = $challengeTimeline->open_call_date_description;
                    $newChallengeTimeline->last_call_date = $challengeTimeline->last_call_date;
                    $newChallengeTimeline->last_call_date_description = $challengeTimeline->last_call_date_description;
                    $newChallengeTimeline->application_deadline_date = $challengeTimeline->application_deadline_date;
                    $newChallengeTimeline->application_deadline_date_description = $challengeTimeline->application_deadline_date_description;
                    $newChallengeTimeline->submission_deadline_date = $challengeTimeline->submission_deadline_date;
                    $newChallengeTimeline->submission_deadline_date_description = $challengeTimeline->submission_deadline_date_description;
                    $newChallengeTimeline->challenge_duration = $challengeTimeline->challenge_duration;
                    $newChallengeTimeline->flexible_date_number = $challengeTimeline->flexible_date_number;
                    $newChallengeTimeline->flexible_date_duration = $challengeTimeline->flexible_date_duration;
                    $newChallengeTimeline->automatic_alert = $challengeTimeline->automatic_alert;
                    $newChallengeTimeline->flexible_expire_deadline = $challengeTimeline->flexible_expire_deadline;
                    $newChallengeTimeline->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengeTemplateTimelines($challengeTemplateId)
    {
        try {
            $challengeTemplateTimelines = ChallengeTemplateTimeline::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateTimelines->isNotEmpty()) {
                $deleteChallengeTemplateTimelines = ChallengeTemplateTimeline::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateTimelines) {
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
