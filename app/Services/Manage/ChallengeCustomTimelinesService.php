<?php

namespace App\Services\Manage;

use App\Models\ChallengeCustomTimelines;
use App\Models\TemplateChallengeCustomeTimeLine;
use Exception;

class ChallengeCustomTimelinesService
{
    public function createChallengeCustomTimelines($request, $challenge)
    {
        try {
            if ($request->timeline_type == 'flexible') {
                if ($request->custom_timelines_title !== null && $request->custom_timelines_date !== null) {
                    foreach ($request->custom_timelines_title as $key => $value) {
                        $custom_date = date('Y-m-d H:i:s', strtotime($request->custom_timelines_date[$key]));
                        $challengeCustomTimeline = new ChallengeCustomTimelines();
                        $challengeCustomTimeline->challenge_id = $challenge;
                        $challengeCustomTimeline->custom_timelines_title = $request->custom_timelines_title[$key];
                        $challengeCustomTimeline->custom_timelines_date = $custom_date;
                        $challengeCustomTimeline->custom_timelines_description = $request->custom_timelines_description[$key];
                        $challengeCustomTimeline->custom_timelines_duration = $request->custom_timelines_duration[$key];
                        $challengeCustomTimeline->schedule_custom_notify = $request->schedule_custom_notify[$key];
                        $challengeCustomTimeline->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallengeCustomTimelines($request, $challenge_id)
    {
        try {
            if ($request->has('timeline_type')) {
                if ($request->timeline_type == 'flexible') {
                    if ($request->custom_timelines_title !== null && $request->custom_timelines_date !== null) {
                        ChallengeCustomTimelines::where('challenge_id', $challenge_id)->delete();
                        foreach ($request->custom_timelines_title as $key => $value) {
                            $custom_date = date('Y-m-d H:i:s', strtotime($request->custom_timelines_date[$key]));
                            $challengeCustomTimeline = new ChallengeCustomTimelines();
                            $challengeCustomTimeline->challenge_id = $challenge_id;
                            $challengeCustomTimeline->custom_timelines_title = $request->custom_timelines_title[$key];
                            $challengeCustomTimeline->custom_timelines_date = $custom_date;
                            $challengeCustomTimeline->custom_timelines_description = $request->custom_timelines_description[$key];
                            $challengeCustomTimeline->custom_timelines_duration = $request->custom_timelines_duration[$key];
                            $challengeCustomTimeline->schedule_custom_notify = $request->schedule_custom_notify[$key];
                            $challengeCustomTimeline->save();
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
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
            return false;
        }
    }

    public function createTemplateChallengeCustomTimeLines($challengeId,$templateChallengeId){
        try {
            $challengeCustomTimelines=ChallengeCustomTimelines::where('challenge_id',$challengeId)->get();
            if($challengeCustomTimelines){
                foreach ($challengeCustomTimelines as $challengeCustomTimeline){
                    $templateChallengeCustomTimeLine=new TemplateChallengeCustomeTimeLine();
                    $templateChallengeCustomTimeLine->template_challenge_id = $templateChallengeId;
                    $templateChallengeCustomTimeLine->custom_timelines_title=$challengeCustomTimeline->custom_timelines_title;
                    $templateChallengeCustomTimeLine->custom_timelines_date=$challengeCustomTimeline->custom_timelines_date;
                    $templateChallengeCustomTimeLine->custom_timelines_description=$challengeCustomTimeline->custom_timelines_description;
                    $templateChallengeCustomTimeLine->custom_timelines_duration=$challengeCustomTimeline->custom_timelines_duration;
                    $templateChallengeCustomTimeLine->schedule_custom_notify=$challengeCustomTimeline->schedule_custom_notify;
                    $templateChallengeCustomTimeLine->save();
                }
            }
            return true;
        }catch (Exception $e) {
            return false;
        }
    }
}
