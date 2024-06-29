<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeAnnouncementRecipient;
use Exception;
use Illuminate\Support\Facades\Schema;

class ChallengeAnnouncementRecipientService
{
    public function getChallengeAnnouncementRecipient($request)
    {
        try {
            if ($request->language == 'en') {
                $challengeAnnouncementRecipients = ChallengeAnnouncementRecipient::select('id', 'title');
            } else {
                $column_name = LanguageColumnHelper::getLanguageColumnName($request->language, 'title');
                //check whether the column exist in the db or not

                if (!$column_name || !Schema::hasColumn('durations', $column_name)) {
                    return false;
                }
                $challengeAnnouncementRecipients = ChallengeAnnouncementRecipient::select('id', $column_name.' as title');
            }
            if ($request->search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $challengeAnnouncementRecipients = $challengeAnnouncementRecipients->where($column_name, 'like', '%'.$request->search.'%');
            }
            $challengeAnnouncementRecipients = $challengeAnnouncementRecipients->take(config('site-settings.dropdown_listing_limit'))->get();
            if (!$challengeAnnouncementRecipients->isEmpty()) {
                return $challengeAnnouncementRecipients;
            }

            return false;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
