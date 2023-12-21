<?php

namespace App\Services\Manage;

use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeTemplateAssessmentCriterias;
use Exception;

class ChallengeTemplateAssessmentCriteriaService
{
    public function createChallengeTemplateAssessmentCriteria($challengeId, $templateChallengeId)
    {
        try {
            $challengeAssessmentCriteria = ChallengeAssessmentCriteria::where('challenge_id', $challengeId)->get();
            foreach ($challengeAssessmentCriteria as $challengeAssessmentCriterion) {
                $challengeTemplateAssessmentCriteria = new ChallengeTemplateAssessmentCriterias();
                $challengeTemplateAssessmentCriteria->template_challenge_id = $templateChallengeId;
                $challengeTemplateAssessmentCriteria->title = $challengeAssessmentCriterion->title;
                $challengeTemplateAssessmentCriteria->score = $challengeAssessmentCriterion->score;
                $challengeTemplateAssessmentCriteria->weight = $challengeAssessmentCriterion->weight;
                $challengeTemplateAssessmentCriteria->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
