<?php

namespace App\Services\Manage;

use App\Models\ChallengeAssessmentCriteria;
use Exception;

class ChallengeAssessmentCriteriaService
{
    public function createChallengeAssessmentCriteria($request, $challenge)
    {
        try {
            if ($request->assessment_title !== null && $request->assessment_score !== null && $request->assessment_weight !== null) {
                foreach ($request->assessment_title as $key => $value) {
                    $challengeAssessmentCriteria = new ChallengeAssessmentCriteria();
                    $challengeAssessmentCriteria->challenge_id = $challenge;
                    $challengeAssessmentCriteria->title = $request->assessment_title[$key];
                    $challengeAssessmentCriteria->score = $request->assessment_score[$key];
                    $challengeAssessmentCriteria->weight = $request->assessment_weight[$key];
                    $challengeAssessmentCriteria->save();
                }
            }

            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
