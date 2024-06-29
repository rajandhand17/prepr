<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTimelines;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ChallengeTimelinesService
{
    public function createChallengeTimelines($request, $challenge_id)
    {
        try {
            $time_line = config('constants.challenge_timeline_type.flexible');
            if (!$request->has('timeline_type')) {
                $request->timeline_type = 'flexible';
            }

            switch ($request->timeline_type) {
                case 'restricted':
                    $time_line = config('constants.challenge_timeline_type.restricted');
                    break;
                case 'flexible':
                    $time_line = config('constants.challenge_timeline_type.flexible');
                    break;
                default:
                    $time_line = config('constants.challenge_timeline_type.flexible');
                    break;
            }

            if ($request->timeline_type == 'restricted') {
                $openDate = date('Y-m-d H:i:s', strtotime($request->open_call_date));
                $lastDate = date('Y-m-d H:i:s', strtotime($request->last_call_date));
                $applicationDate = date('Y-m-d H:i:s', strtotime($request->application_deadline_date));
                $submissionDate = date('Y-m-d H:i:s', strtotime($request->submission_deadline_date));
                $open_date = Carbon::parse($openDate);
                $close_date = Carbon::parse($submissionDate);
                $challenge_duration = $open_date->diffInDays($close_date);

                $challengeRestrictedTimeLine = new ChallengeTimelines();
                $challengeRestrictedTimeLine->challenge_id = $challenge_id;
                $challengeRestrictedTimeLine->timeline_type = $time_line;
                $challengeRestrictedTimeLine->open_call_date = $openDate;
                $challengeRestrictedTimeLine->open_call_date_description = $request->open_call_date_description ?? null;
                $challengeRestrictedTimeLine->last_call_date = $lastDate;
                $challengeRestrictedTimeLine->last_call_date_description = $request->last_call_date_description ?? null;
                $challengeRestrictedTimeLine->application_deadline_date = $applicationDate;
                $challengeRestrictedTimeLine->application_deadline_date_description = $request->application_deadline_date_description ?? null;
                $challengeRestrictedTimeLine->submission_deadline_date = $submissionDate;
                $challengeRestrictedTimeLine->submission_deadline_date_description = $request->submission_deadline_date_description ?? null;
                $challengeRestrictedTimeLine->challenge_duration = $challenge_duration;
                $challengeRestrictedTimeLine->save();
            } elseif ($request->timeline_type == 'flexible') {
                $flexibleDeadlineDate = null;
                if ($request->flexible_expire_deadline) {
                    $flexibleDeadlineDate = date('Y-m-d H:i:s', strtotime($request->flexible_expire_deadline));
                }
                $flexible_date_number = $request->flexible_date_number ?? 2;
                $flexible_date_duration = $request->flexible_date_duration ?? 'weeks';
                $automatic_alert = $request->automatic_alert ?? '0';

                $challengeFlexibleTimeLine = new ChallengeTimelines();
                $challengeFlexibleTimeLine->challenge_id = $challenge_id;
                $flexible_date_number && $challengeFlexibleTimeLine->flexible_date_number = $flexible_date_number;
                $flexible_date_duration && $challengeFlexibleTimeLine->flexible_date_duration = $flexible_date_duration;
                $challengeFlexibleTimeLine->automatic_alert = $automatic_alert;
                $flexibleDeadlineDate && $challengeFlexibleTimeLine->flexible_expire_deadline = $flexibleDeadlineDate;
                $challengeFlexibleTimeLine->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeTimelines in ChallengeTimelinesService.php: '.$e->getMessage());

            return false;
        }
    }

    public function updateChallengeTimelines($request, $challenge_id)
    {
        try {
            if ($request->has('timeline_type')) {
                switch ($request->timeline_type) {
                    case 'restricted':
                        $time_line = config('constants.challenge_timeline_type.restricted');
                        break;
                    case 'flexible':
                        $time_line = config('constants.challenge_timeline_type.flexible');
                        break;
                    default:
                        $time_line = config('constants.challenge_timeline_type.flexible');
                        break;
                }

                ChallengeTimelines::where('challenge_id', $challenge_id)->delete();
                if ($request->timeline_type == 'restricted') {
                    $openDate = date('Y-m-d H:i:s', strtotime($request->open_call_date));
                    $lastDate = date('Y-m-d H:i:s', strtotime($request->last_call_date));
                    $applicationDate = date('Y-m-d H:i:s', strtotime($request->application_deadline_date));
                    $submissionDate = date('Y-m-d H:i:s', strtotime($request->submission_deadline_date));
                    $open_date = Carbon::parse($openDate);
                    $close_date = Carbon::parse($submissionDate);
                    $challenge_duration = $open_date->diffInDays($close_date);

                    $challengeRestrictedTimeLine = new ChallengeTimelines();
                    $challengeRestrictedTimeLine->challenge_id = $challenge_id;
                    $challengeRestrictedTimeLine->timeline_type = $time_line;
                    $challengeRestrictedTimeLine->open_call_date = $openDate;
                    $challengeRestrictedTimeLine->open_call_date_description = $request->open_call_date_description ?? null;
                    $challengeRestrictedTimeLine->last_call_date = $lastDate;
                    $challengeRestrictedTimeLine->last_call_date_description = $request->last_call_date_description ?? null;
                    $challengeRestrictedTimeLine->application_deadline_date = $applicationDate;
                    $challengeRestrictedTimeLine->application_deadline_date_description = $request->application_deadline_date_description ?? null;
                    $challengeRestrictedTimeLine->submission_deadline_date = $submissionDate;
                    $challengeRestrictedTimeLine->submission_deadline_date_description = $request->submission_deadline_date_description ?? null;
                    $challengeRestrictedTimeLine->challenge_duration = $challenge_duration;
                    $challengeRestrictedTimeLine->save();
                } elseif ($request->timeline_type == 'flexible') {
                    $flexibleDeadlineDate = null;
                    if ($request->flexible_expire_deadline) {
                        $flexibleDeadlineDate = date('Y-m-d H:i:s', strtotime($request->flexible_expire_deadline));
                    }
                    $challengeFlexibleTimeLine = new ChallengeTimelines();
                    $challengeFlexibleTimeLine->challenge_id = $challenge_id;
                    $challengeFlexibleTimeLine->flexible_date_number = $request->flexible_date_number;
                    $challengeFlexibleTimeLine->flexible_date_duration = $request->flexible_date_duration;
                    $challengeFlexibleTimeLine->automatic_alert = $request->automatic_alert;
                    $challengeFlexibleTimeLine->flexible_expire_deadline = $request->$flexibleDeadlineDate;
                    $challengeFlexibleTimeLine->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeTimelines($originalChallengeProjectTemplate, $clonedChallengeId)
    {
        try {
            if ($originalChallengeProjectTemplate) {
                $cloneIncentiveAchievement = $originalChallengeProjectTemplate->replicate();
                $cloneIncentiveAchievement->challenge_id = $clonedChallengeId;
                $cloneIncentiveAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
