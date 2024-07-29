<?php

namespace App\Services\Maestro;

use App\Models\ChallengeTimelines;
use Exception;

class ChallengeTimelineService
{
    public static function challengeTimelinesSave($request, $challenge)
    {
        try {
            if ($request->timeline_type == '1') {
                return ChallengeTimelines::create(['challenge_id' => $challenge->id, 'timeline_type' => $request->timeline_type, 'open_call_date' => $request->open_call_date, 'open_call_date_description' => $request->open_call_date_description, 'last_call_date' => $request->last_call_date, 'last_call_date_description' => $request->last_call_date_description, 'application_deadline_date' => $request->application_deadline_date, 'application_deadline_date_description' => $request->application_deadline_date_description, 'submission_deadline_date' => $request->submission_deadline_date, 'submission_deadline_date_description' => $request->submission_deadline_date_description]);
            } elseif ($request->timeline_type == '0') {
                return ChallengeTimelines::create(['challenge_id' => $challenge->id, 'timeline_type' => $request->timeline_type, 'flexible_date_number' => $request->flexible_date_number, 'flexible_date_duration' => $request->flexible_date_duration, 'flexible_expire_deadline' => $request->flexible_expire_deadline, 'automatic_alert' => $request->automatic_alert]);
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
