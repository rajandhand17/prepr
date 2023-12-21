<?php

namespace App\Services\Manage;

use App\Models\ChallengeAssessmentCriteria;
use App\Models\TemplateChallengeAssessmentCriterias;
use Exception;

class ChallengeTemplateAssessmentService
{
    public function createChallengeTemplateAssessment($challengeId, $templateChallengeId)
    {
        try {
            $challengeAssessmentCriteria = ChallengeAssessmentCriteria::where('challenge_id', $challengeId)->get();
            foreach ($challengeAssessmentCriteria as $challengeAssessmentCriterion) {
                $templateChallengeAssessmentCriteria = new TemplateChallengeAssessmentCriterias();
                $templateChallengeAssessmentCriteria->template_challenge_id = $templateChallengeId;
                $templateChallengeAssessmentCriteria->title = $challengeAssessmentCriterion->title;
                $templateChallengeAssessmentCriteria->score = $challengeAssessmentCriterion->score;
                $templateChallengeAssessmentCriteria->weight = $challengeAssessmentCriterion->weight;
                $templateChallengeAssessmentCriteria->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
