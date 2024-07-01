<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAssessmentCriteria;
use Exception;

class ChallengeAssessmentCriteriaService
{
    public function createChallengeAssessmentCriteria($request, $challenge, $challengeAssessment = null)
    {
        try {
            if (!empty($request->assessment_type) && $request->assessment_title !== null && $request->assessment_score !== null && $request->assessment_weight !== null) {
                foreach ($request->assessment_title as $key => $value) {
                    $challengeAssessmentCriteria = new ChallengeAssessmentCriteria();
                    $challengeAssessmentCriteria->challenge_id = $challenge;
                    $challengeAssessmentCriteria->assessment_id = $challengeAssessment->id;
                    $challengeAssessmentCriteria->title = $value;
                    $challengeAssessmentCriteria->description = $request->assessment_description[$key] ?? null;
                    $challengeAssessmentCriteria->score = $request->assessment_score[$key];
                    $challengeAssessmentCriteria->weight = $request->assessment_weight[$key];
                    $challengeAssessmentCriteria->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallengeAssessmentCriteria($request, $challenge_id, $updateChallengeAssessment)
    {
        try {
            ChallengeAssessmentCriteria::where('challenge_id', $challenge_id)->delete();
            if ($request->assessment_type == 'none') {
                return true;
            }

            if ($request->assessment_type != 'none') {
                foreach ($request->assessment_title as $key => $value) {
                    $challengeAssessmentCriteria = new ChallengeAssessmentCriteria();
                    $challengeAssessmentCriteria->challenge_id = $challenge_id;
                    $challengeAssessmentCriteria->assessment_id = $updateChallengeAssessment->id;
                    $challengeAssessmentCriteria->title = $request->assessment_title[$key];
                    $challengeAssessmentCriteria->description = $request->assessment_description[$key] ?? null;
                    $challengeAssessmentCriteria->score = $request->assessment_score[$key];
                    $challengeAssessmentCriteria->weight = $request->assessment_weight[$key];
                    $challengeAssessmentCriteria->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneChallengeAssessmentCriteria($originalChallengeAssessmentCriteria, $clonedChallengeId)
    {
        try {
            $originalChallengeAssessmentCriteria->each(function ($challenge_assessment_criteria) use ($clonedChallengeId) {
                if ($challenge_assessment_criteria) {
                    $cloneAssessmentCriteria = $challenge_assessment_criteria->replicate();
                    $cloneAssessmentCriteria->challenge_id = $clonedChallengeId;
                    $cloneAssessmentCriteria->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
