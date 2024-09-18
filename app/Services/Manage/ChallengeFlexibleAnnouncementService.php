<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeFlexibleAnnouncement;
use Exception;

class ChallengeFlexibleAnnouncementService
{
    public static function storeChallengeFlexibleAnnouncement($request, $challengeId, $challengeCustomTimelineId, $key)
    {
        try {
            if ($request->has('schedule_custom_notify') && $request->schedule_custom_notify == 'yes') {

                    $sendAnnouncementChannelMedium = config('constants.challenge_flexible_announcement_by.email');
                    switch ($request->custom_announcement_type[$key]) {
                        case 'email':
                            $sendAnnouncementChannelMedium = config('constants.challenge_flexible_announcement_by.email');
                            break;
                        case 'notification':
                            $sendAnnouncementChannelMedium = config('constants.challenge_flexible_announcement_by.notification');
                            break;
                    }

                    $schedule_status = config('constants.challenge_flexible_announcement_by.immediately');
                    switch ($request->schedule_status) {
                        case 'immediately':
                            $schedule_status = config('constants.challenge_flexible_announcement_by.immediately');
                            break;
                        case 'custom':
                            $schedule_status = config('constants.challenge_flexible_announcement_by.custom');
                            break;
                    }

                    $challengeFlexibleAnnouncement = new ChallengeFlexibleAnnouncement();
                    $challengeFlexibleAnnouncement->challenge_id = $challengeId;
                    $challengeFlexibleAnnouncement->challenge_custom_timeline_id = $challengeCustomTimelineId;
                    $challengeFlexibleAnnouncement->custom_announcement_type = $sendAnnouncementChannelMedium;
                    $challengeFlexibleAnnouncement->custom_announcement_number = $request->custom_announcement_number[$key] ?? 2;
                    $challengeFlexibleAnnouncement->custom_announcement_duration = $request->custom_announcement_duration[$key] ?? 'weeks';
                    $challengeFlexibleAnnouncement->custom_announcement_title = $request->custom_announcement_title[$key] ?? null;
                    $challengeFlexibleAnnouncement->custom_announcement_description = $request->custom_announcement_description[$key] ?? null;
                    $challengeFlexibleAnnouncement->custom_announcement_schedule_status = $schedule_status;
                    $challengeFlexibleAnnouncement->save();

            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
