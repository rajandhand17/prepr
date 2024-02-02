<?php

namespace App\Services\Manage;

use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeTemplateAssessmentCriterias;
use Exception;

class ChallengeTemplateAssessmentCriteriaService
{
    public function addChallengeTemplateAssessmentCriteria($challengeId, $templateChallengeId)
    {
        try {
            $challengeAssessmentCriteria = ChallengeAssessmentCriteria::where('challenge_id', $challengeId)->get();
            foreach ($challengeAssessmentCriteria as $challengeAssessmentCriterion) {
                $challengeTemplateAssessmentCriteria = new ChallengeTemplateAssessmentCriterias();
                $challengeTemplateAssessmentCriteria->challenge_template_id = $templateChallengeId;
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

    public function redeemChallengeTemplateAssessmentCriteria($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateAssessmentCriterias = ChallengeTemplateAssessmentCriterias::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateAssessmentCriterias)) {
                foreach ($checkChallengeTemplateAssessmentCriterias as $challengeTemplateAssessmentCriteria) {
                    $newChallengeAssessmentCriterias = new ChallengeAssessmentCriteria();
                    $newChallengeAssessmentCriterias->challenge_id = $redeemChallengeId;
                    $newChallengeAssessmentCriterias->title = $challengeTemplateAssessmentCriteria->title;
                    $newChallengeAssessmentCriterias->score = $challengeTemplateAssessmentCriteria->score;
                    $newChallengeAssessmentCriterias->weight = $challengeTemplateAssessmentCriteria->weight;
                    $newChallengeAssessmentCriterias->save();
                }
            }

            return true;
        } catch (Exception $e) {
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
            return false;
        }
    }
}
