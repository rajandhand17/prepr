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
            if ($request->has('schedule_custom_notify') && $request->schedule_custom_notify[$key] === 'yes') {
                // Configuration mappings
                $announcementTypes = [
                    'email'        => config('constants.challenge_flexible_announcement_by.email'),
                    'notification' => config('constants.challenge_flexible_announcement_by.notification'),
                ];

                $scheduleStatuses = [
                    'immediately' => config('constants.challenge_flexible_announcement_by.immediately'),
                    'custom'      => config('constants.challenge_flexible_announcement_by.custom'),
                ];

                // Announcement type and schedule status determination
                $sendAnnouncementChannelMedium = $announcementTypes[$request->custom_announcement_type[$key]] ?? config('constants.challenge_flexible_announcement_by.email');

                $schedule_status = $scheduleStatuses[$request->schedule_status[$key]] ?? config('constants.challenge_flexible_announcement_by.immediately');

                // Create and save the announcement
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
