<?php

namespace App\Services\Manage;

use App\Models\ChallengeProjectTemplate;
use App\Models\ChallengeTemplateProjectTemplate;
use Exception;

class ChallengeTemplateProjectTemplateService
{
    public function addChallengeTemplateProjectTemplate($challengeId, $templateChallengeId)
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
            return false;
        }
    }

    public function redeemChallengeTemplateProjectTemplate($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateProjectTemplates = ChallengeTemplateProjectTemplate::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateProjectTemplates)) {
                foreach ($checkChallengeTemplateProjectTemplates as $challengeTemplateProjectTemplate) {
                    $newChallengeProjectTemplate = new ChallengeProjectTemplate();
                    $newChallengeProjectTemplate->challenge_id = $redeemChallengeId;
                    $newChallengeProjectTemplate->template_id = $challengeTemplateProjectTemplate->template_id;
                    $newChallengeProjectTemplate->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
