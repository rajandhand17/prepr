<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeTask;

class ChallengeTaskService
{
    public static function storeChallengeTasks($templateId, $request)
    {
        try {
            $language = $request->language;
            if ($language == 'en') {
                $columnTitle = 'title';
            } else {
                $columnTitle = LanguageColumnHelper::getLanguageColumnName($language, 'title');
            }

            foreach ($request->task_questions as $key => $value) {
                $challengePitches = ChallengeTask::where(['template_id' => $templateId, $columnTitle => $request->task_questions[$key]])->first();
                if ($challengePitches) {
                    $storeChallengePitches = $challengePitches;
                } else {
                    $storeChallengePitches = new ChallengeTask();
                }

                $storeChallengePitches->template_id = $templateId;
                $storeChallengePitches->$columnTitle = $value;
                $storeChallengePitches->save();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
