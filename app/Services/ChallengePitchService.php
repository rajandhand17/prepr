<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengePitch;

class ChallengePitchService
{
    public static function storeChallengePitches($templateId, $request)
    {
        try {
            $language = $request->language;
            if ($language == 'en') {
                $columnTitle = 'title';
                $columnDescription = 'description';
                $columnDefaultDescription = 'Write your answer here...';
            } else {
                $columnTitle = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                $columnDescription = LanguageColumnHelper::getLanguageColumnName($language, 'description');
                $columnDefaultDescription = 'Écrivez la réponse ici...';
            }

            foreach ($request->pitch_questions as $key => $value) {
                $challengePitches = ChallengePitch::where(['template_id' => $templateId, $columnTitle => $request->pitch_questions[$key], $columnDescription => $request->pitch_questions_description[$key]])->first();
                if ($challengePitches) {
                    $storeChallengePitches = $challengePitches;
                } else {
                    $storeChallengePitches = new ChallengePitch();
                }

                $storeChallengePitches->template_id = $templateId;
                $storeChallengePitches->$columnTitle = $value;
                $storeChallengePitches->$columnDescription = $request->pitch_questions_description[$key] ?? $columnDefaultDescription;
                $storeChallengePitches->save();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
