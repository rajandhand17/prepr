<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeProjectTemplate;
use App\Models\ChallengeTemplateProjectTemplate;
use Exception;

class ChallengeTemplateProjectTemplateService
{
    public static function addChallengeTemplateProjectTemplate($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeProjectTemplate as $challengeProjectTemplate) {
                $challengeTemplateProjectTemplate = new ChallengeTemplateProjectTemplate();
                $challengeTemplateProjectTemplate->challenge_template_id = $templateChallengeId;
                $challengeTemplateProjectTemplate->template_id = $challengeProjectTemplate->template_id;
                $challengeTemplateProjectTemplate->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
