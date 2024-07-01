<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeTemplateAssessmentCriterias;
use Exception;

class ChallengeTemplateAssessmentCriteriaService
{
    public function addChallengeTemplateAssessmentCriteria($challengeId, $templateChallengeId, $templateChallengeAssessmentId)
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

    public function redeemChallengeTemplateAssessmentCriteria($redeemChallengeId, $challengeTemplateId, $challengeAsessmentData)
    {
        try {
            $checkChallengeTemplateAssessmentCriterias = ChallengeTemplateAssessmentCriterias::where('challenge_template_id', $challengeTemplateId)->first();
            if ($checkChallengeTemplateAssessmentCriterias) {
                $newChallengeAssessmentCriterias = new ChallengeAssessmentCriteria();
                $newChallengeAssessmentCriterias->challenge_id = $redeemChallengeId;
                $newChallengeAssessmentCriterias->assessment_id = $challengeAsessmentData->id;
                $newChallengeAssessmentCriterias->title = $checkChallengeTemplateAssessmentCriterias->title;
                $newChallengeAssessmentCriterias->description = $checkChallengeTemplateAssessmentCriterias->description;
                $newChallengeAssessmentCriterias->score = $checkChallengeTemplateAssessmentCriterias->score;
                $newChallengeAssessmentCriterias->weight = $checkChallengeTemplateAssessmentCriterias->weight;
                $newChallengeAssessmentCriterias->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengeTemplateAssessmentCriteria($challengeTemplateId)
    {
        try {
            $challengeTemplateAssessmentCriteria = ChallengeTemplateAssessmentCriterias::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateAssessmentCriteria->isNotEmpty()) {
                $deleteChallengeTemplateAssessmentCriteria = ChallengeTemplateAssessmentCriterias::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateAssessmentCriteria) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
