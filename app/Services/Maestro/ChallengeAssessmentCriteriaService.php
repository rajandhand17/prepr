<?php

namespace App\Services\Maestro;

use App\Models\ChallengeAssessmentCriteria;
use Exception;

class ChallengeAssessmentCriteriaService
{
    public static function getCriteria($challengeId)
    {
        try {
            $criteria = ChallengeAssessmentCriteria::select('title', 'score', 'weight', 'assessment_id')->where('challenge_id', $challengeId)->get();
            if ($criteria) {
                return $criteria;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function addUpdateAssessmentCriteria($request)
    {
        try {
            switch ($request->assessment_type) {
                case '0':
                    $criteria = [];
                    break;
                case '1':
                    $criteria = array_map(null, $request->creteria_title, $request->score, $request->weight);
                    break;
                case '2':
                    $criteria = array_map(null, $request->creteria_title, $request->score, $request->weight);
                    break;
                default:
                    $criteria = [];
            }

            if (ChallengeAssessmentCriteria::where('challenge_id', (int) $request->challenge_id)->exists()) {
                ChallengeAssessmentCriteria::where('challenge_id', (int) $request->challenge_id)->delete();
            }
            if (!empty($criteria)) {
                $criteriaNewArray = [];
                foreach ($criteria as $key => $criteriaObj) {
                    $criteriaObjData['challenge_id'] = (int) $request->challenge_id;
                    $criteriaObjData['assessment_id'] = (int) $request->assessment_id;
                    $criteriaObjData['title'] = $criteriaObj[0];
                    $criteriaObjData['score'] = (int) $criteriaObj[1];
                    $criteriaObjData['weight'] = (int) $criteriaObj[2];
                    $criteriaNewArray[] = $criteriaObjData;
                }
                ChallengeAssessmentCriteria::insert($criteriaNewArray);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
