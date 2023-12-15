<?php

namespace App\Services\Manage;

use App\Models\ChallengeProjectTemplate;
use App\Models\TemplateChallengeProjectTemplate;
use Exception;

class ChallengeProjectTemplateService
{
    public function createChallengeProjectTemplate($request, $challenge)
    {
        try {
            $challengeProjectTemplate = new ChallengeProjectTemplate();
            $challengeProjectTemplate->challenge_id = $challenge;
            $challengeProjectTemplate->template_id = $request->template_id;
            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallengeProjectTemplate($request, $challenge_id)
    {
        try {
            $challengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challenge_id)->first();
            $challengeProjectTemplate->template_id = ($request->has('template_id')) ? $request->template_id : $challengeProjectTemplate->template_id;
            $challengeProjectTemplate->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function cloneChallengeProjectTemplate($originalChallengeProjectTemplate, $clonedChallengeId)
    {
        try {
            if ($originalChallengeProjectTemplate) {
                $cloneIncentiveAchievement = $originalChallengeProjectTemplate->replicate();
                $cloneIncentiveAchievement->challenge_id = $clonedChallengeId;
                $cloneIncentiveAchievement->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createTemplateChallengeProjectTemplate($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeProjectTemplate = ChallengeProjectTemplate::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeProjectTemplate as $challengeProjectTemplate) {
                $templateChallengeProjectTemplate = new TemplateChallengeProjectTemplate();
                $templateChallengeProjectTemplate->template_challenge_id = $templateChallengeId;
                $templateChallengeProjectTemplate->title = $challengeProjectTemplate->title;
                $templateChallengeProjectTemplate->score = $challengeProjectTemplate->score;
                $templateChallengeProjectTemplate->weight = $challengeProjectTemplate->weight;
                $templateChallengeProjectTemplate->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
