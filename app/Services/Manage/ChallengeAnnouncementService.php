<?php

namespace App\Services\Manage;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeAnnouncement;
use App\Models\ChallengeAnnouncementRecipient;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Schema;

class ChallengeAnnouncementService
{
    public function createChallengeAnnouncement($challengeId, $request)
    {
        try {
            $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.both');
            switch ($request->sent_by) {
                case 'email':
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.email');
                    break;
                case 'inbox':
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.inbox');
                    break;
                case 'both':
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.both');
                    break;
                default:
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.both');
                    break;
            }

            $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.send');
            switch ($request->status) {
                case 'send':
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.send');
                    break;
                case 'draft':
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.draft');
                    break;
                case 'scheduled':
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.scheduled');
                    break;
                default:
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.send');
                    break;
            }

            $schedule_date = $request->schedule_at !== null ? date('Y-m-d H:i:s', strtotime($request->schedule_at)) : Carbon::now()->toDateTimeString();

            $challengeAnnouncement = new ChallengeAnnouncement();
            $challengeAnnouncement->challenge_id = $challengeId;
            $challengeAnnouncement->subject = $request->subject;
            $challengeAnnouncement->to_recipient_ids = $request->to_recipient_ids;
            $challengeAnnouncement->sent_by = $sendAnnouncementChannelMedium;
            $challengeAnnouncement->description = $request->description;
            $challengeAnnouncement->schedule_at = $schedule_date;
            $challengeAnnouncement->status = $sendAnnouncementSendStatus;
            $challengeAnnouncement->save();

            return $challengeAnnouncement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeAnnouncementByID($language = 'en', $announcement_recipient_id)
    {
        try {
            if ($language == 'en') {
                $announcement_recipient = ChallengeAnnouncementRecipient::select('id', 'title');
            } else {
                //get column title based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('to_recipient_ids', $column_name)) {
                    return false;
                }
                $announcement_recipient = ChallengeAnnouncementRecipient::select('id', $column_name.' as title');
            }
            $challenge_announcement = $announcement_recipient->find($announcement_recipient_id);

            return $challenge_announcement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteChallengeAnnouncement($challengeAnnouncementId)
    {
        try {
            ChallengeAnnouncement::find($challengeAnnouncementId)->delete();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengeAnnouncement($challengeAnnouncementId)
    {
        try {
            $fetchChallengeAnnouncement = ChallengeAnnouncement::find($challengeAnnouncementId);

            return $fetchChallengeAnnouncement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
