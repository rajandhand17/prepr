<?php

namespace App\Services\Maestro;

use App\Models\ChallengeTimelines;
use Carbon\Carbon;
use Exception;

class ChallengeTimelineService
{
    public static function challengeTimelinesSave($request, $challenge)
    {
        try {
            $open_call_date = !empty($request->open_call_date) ? Carbon::createFromFormat('m/d/Y', $request->open_call_date)->format('Y-m-d 00:00:00') : null;
            $last_call_date = !empty($request->last_call_date) ? Carbon::createFromFormat('m/d/Y', $request->last_call_date)->format('Y-m-d 00:00:00') : null;
            $application_deadline_date = !empty($request->application_deadline_date) ? Carbon::createFromFormat('m/d/Y', $request->application_deadline_date)->format('Y-m-d 00:00:00') : null;
            $submission_deadline_date = !empty($request->submission_deadline_date) ? Carbon::createFromFormat('m/d/Y', $request->submission_deadline_date)->format('Y-m-d 00:00:00') : null;
            
            if(ChallengeTimelines::where('challenge_id',$challenge->id)->exists()){
                ChallengeTimelines::where('challenge_id',$challenge->id)->delete();
            }
            
            if ($request->timeline_type == '1') {
                ChallengeTimelines::create(['challenge_id' => $challenge->id, 'timeline_type' => $request->timeline_type, 'open_call_date' => $open_call_date, 'open_call_date_description' => $request->open_call_date_description, 'last_call_date' => $last_call_date, 'last_call_date_description' => $request->last_call_date_description, 'application_deadline_date' => $application_deadline_date, 'application_deadline_date_description' => $request->application_deadline_date_description, 'submission_deadline_date' => $submission_deadline_date, 'submission_deadline_date_description' => $request->submission_deadline_date_description]);
            } elseif ($request->timeline_type == '0') {
                ChallengeTimelines::create(['challenge_id' => $challenge->id, 'timeline_type' => $request->timeline_type, 'flexible_date_number' => $request->flexible_date_number, 'flexible_date_duration' => $request->flexible_date_duration, 'flexible_expire_deadline' => $request->flexible_expire_deadline, 'automatic_alert' => $request->automatic_alert]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeTimeLines($challenge)
    {
        try {
            return ChallengeTimelines::where('challenge_id',$challenge->id)->first();
        } catch (Exception $e) {
            return false;
        }
    }
}
