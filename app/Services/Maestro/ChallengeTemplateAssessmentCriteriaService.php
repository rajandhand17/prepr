<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeTemplateAssessmentCriterias;
use Exception;

class ChallengeTemplateAssessmentCriteriaService
{
    public static function addChallengeTemplateAssessmentCriteria($challengeId, $templateChallengeId, $templateChallengeAssessmentId)
    {
        try {
            $challengeAssessmentCriteria = ChallengeAssessmentCriteria::where('challenge_id', $challengeId)->get();
            if ($templateChallengeAssessmentId != null) {
                foreach ($challengeAssessmentCriteria as $challengeAssessmentCriterion) {
                    $challengeTemplateAssessmentCriteria = new ChallengeTemplateAssessmentCriterias();
                    $challengeTemplateAssessmentCriteria->challenge_template_id = $templateChallengeId;
                    $challengeTemplateAssessmentCriteria->template_assessment_id = $templateChallengeAssessmentId->id;
                    $challengeTemplateAssessmentCriteria->title = $challengeAssessmentCriterion->title;
                    $challengeTemplateAssessmentCriteria->description = $challengeAssessmentCriterion->description;
                    $challengeTemplateAssessmentCriteria->score = $challengeAssessmentCriterion->score;
                    $challengeTemplateAssessmentCriteria->weight = $challengeAssessmentCriterion->weight;
                    $challengeTemplateAssessmentCriteria->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
