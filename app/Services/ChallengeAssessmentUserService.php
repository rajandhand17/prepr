<?php

namespace App\Services;

use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeAssessmentUser;
use Exception;

class ChallengeAssessmentUserService
{
    public function addProjectEvaluation($challengeAssessment, $projectData, $userData, $request)
    {
        try {
            $assessmentStatus = $request->status == 'publish' ? config('constants.challenge_status.publish') : config('constants.challenge_status.draft');

            if (isset($request->criteria_id) && isset($request->score) && isset($request->comment)) {
                foreach ($request->criteria_id as $key => $criteriaId) {
                    $challengeAssessmentUser = ChallengeAssessmentUser::updateOrCreate(
                        [
                            'criteria_id' => $criteriaId,
                            'project_id'  => $projectData->id,
                            'user_id'     => $userData->id,
                        ],
                        [
                            'score'            => $request->score[$key],
                            'comment'          => $request->comment[$key],
                            'criteria_comment' => $request->criteria_comment,
                            'status'           => $assessmentStatus,
                        ]
                    );
                }
                if (!$challengeAssessmentUser) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getcriteriaDataBasedOnId($criteriaData, $projectId)
    {
        try {
            $challenge_assessment_criteria = ChallengeAssessmentCriteria::select('id', 'title', 'score', 'weight')->where('id', $criteriaData->id)->first();

            $check_assessment_criteria = ChallengeAssessmentUser::where(['criteria_id' => $criteriaData->id, 'project_id' => $projectId, 'user_id' => auth()->user()->id])->first();

            $challenge_assessment_criteria->score_received = null;
            $challenge_assessment_criteria->comment = null;
            $challenge_assessment_criteria->status = null;
            $challenge_assessment_criteria->criteria_comment = null;

            if ($check_assessment_criteria) {
                $challenge_assessment_criteria->score_received = ($check_assessment_criteria->score !== null) ? $check_assessment_criteria->score : null;
                $challenge_assessment_criteria->comment = ($check_assessment_criteria->comment !== null) ? $check_assessment_criteria->comment : null;
                $challenge_assessment_criteria->status = ($check_assessment_criteria->status !== null && $check_assessment_criteria->status == '1') ? 'publish' : 'draft';
                $challenge_assessment_criteria->criteria_comment = ($check_assessment_criteria->criteria_comment !== null) ? $check_assessment_criteria->criteria_comment : null;
            }

            return $challenge_assessment_criteria;
        } catch (Exception $e) {
            return false;
        }
    }
}
