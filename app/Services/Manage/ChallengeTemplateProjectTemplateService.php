<?php

namespace App\Services\Manage;

use App\Models\ChallengeProjectTemplate;
use App\Models\TemplateChallengeProjectTemplate;
use Exception;

class ChallengeTemplateProjectTemplateService
{
    public function createChallengeTemplateProjectTemplate($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeProjectTemplate as $challengeProjectTemplate) {
                $templateChallengeProjectTemplate = new TemplateChallengeProjectTemplate();
                $templateChallengeProjectTemplate->template_challenge_id = $templateChallengeId;
                $templateChallengeProjectTemplate->template_id = $challengeProjectTemplate->template_id;
                $templateChallengeProjectTemplate->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
