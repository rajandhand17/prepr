<?php

namespace App\Services\Manage;

use App\Models\ChallengeProjectTemplate;
use App\Models\ChallengeTemplateProjectTemplate;
use Exception;

class ChallengeTemplateProjectTemplateService
{
    public function createChallengeTemplateProjectTemplate($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeProjectTemplate as $challengeProjectTemplate) {
                $challengeTemplateProjectTemplate = new ChallengeTemplateProjectTemplate();
                $challengeTemplateProjectTemplate->template_challenge_id = $templateChallengeId;
                $challengeTemplateProjectTemplate->template_id = $challengeProjectTemplate->template_id;
                $challengeTemplateProjectTemplate->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
