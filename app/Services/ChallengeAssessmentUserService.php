<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAssessmentCriteria;
use App\Models\ChallengeAssessmentUser;
use Exception;

class ChallengeAssessmentUserService
{
    public function addProjectEvaluation($challengeAssessment = null, $projectData = null, $userData = null, $request)
    {
        try {
            $assessmentStatus = $request->status == 'published' ? config('constants.challenge_status.publish') : config('constants.challenge_status.draft');

            if (isset($request->criteria_id) && isset($request->score) && isset($request->comment)) {
                foreach ($request->criteria_id as $key => $criteriaId) {
                    $challengeAssessmentUser = ChallengeAssessmentUser::updateOrCreate(
                        [
                            'criteria_id' => $criteriaId,
                            'project_id'  => $request->project_id ?? $projectData->id,
                            'user_id'     => $request->user_id ?? $userData->id,
                        ],
                        [
                            'score'            => $request->score[$key],
                            'comment'          => $request->comment[$key] ?? null,
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getcriteriaDataBasedOnId($criteriaData, $projectId, $userId)
    {
        try {
            $challenge_assessment_criteria = ChallengeAssessmentCriteria::select('id', 'title', 'description', 'score', 'weight')->where('id', $criteriaData->id)->first();

            $check_assessment_criteria = ChallengeAssessmentUser::where(['criteria_id' => $criteriaData->id, 'project_id' => $projectId, 'user_id' => $userId])->first();

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectAssessmentData($projectData, $userId)
    {
        try {
            $project_assessment = null;
            $assessment_status = 'pending';
            $assessment_over_all_comment = null;
            $assessment_attachment = null;

            if ($projectData->getProjectAssessment) {
                $assessment_attachment = $projectData->getProjectAssessment->attachments;
                $project_assessment = $projectData->getProjectAssessment->getAssessmentCriterias->map(function ($criteria) use ($projectData, $userId) {
                    $criteriaData = ChallengeAssessmentUserService::getcriteriaDataBasedOnId($criteria, $projectData->id, $userId);

                    return [
                        'id'                => $criteriaData->id,
                        'title'             => $criteriaData->title,
                        'description'       => $criteriaData->description,
                        'score'             => $criteriaData->score,
                        'weight'            => $criteriaData->weight,
                        'score_received'    => $criteriaData->score_received,
                        'comment'           => $criteriaData->comment,
                        'status'            => $criteriaData->status,
                        'criteria_comment'  => $criteriaData->criteria_comment,
                    ];
                });
            }

            if ($project_assessment != null && $project_assessment->isNotEmpty()) {
                $assessmentStatusCheck = $project_assessment->pluck('status');
                $check = $assessmentStatusCheck->contains(null) || $assessmentStatusCheck->contains('draft');
                switch ($check) {
                    case true:
                        $assessment_status = 'draft';
                        break;

                    case false:
                        $assessment_status = 'published';
                        break;

                    default:
                        $assessment_status = 'pending';
                        break;
                }
            }

            if ($project_assessment != null && $project_assessment->isNotEmpty()) {
                $assessmentComment = $project_assessment->pluck('criteria_comment')->unique();
                $assessment_over_all_comment = $assessmentComment[0];
            }

            switch ($projectData->getProjectAssessment->assessment_type) {
                case '3':
                    $assessment = 'ai';
                    break;
                case '2':
                    $assessment = 'closed';
                    break;
                case '1':
                    $assessment = 'open';
                    break;
                default:
                    $assessment = 'none';
                    break;
            }

            return [
                'assessment_type'               => $assessment,
                'assessment_attachments'        => $assessment_attachment,
                'assessment_status'             => $assessment_status,
                'assessment_over_all_comment'   => $assessment_over_all_comment,
                'assessment_scoring_data'       => $project_assessment,
            ];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkChallengeProjectAssessment($projectDataId, $userData)
    {
        try {
            $checkChallengeProjectAssessment = ChallengeAssessmentUser::where(['project_id' => $projectDataId, 'user_id' => $userData->id])->get();
            if (!empty($checkChallengeProjectAssessment)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteChallengeProjectAssessment($projectDataId, $userData)
    {
        try {
            $checkChallengeProjectAssessment = ChallengeAssessmentUser::where(['project_id' => $projectDataId, 'user_id' => $userData->id])->get();
            if (!empty($checkChallengeProjectAssessment)) {
                ChallengeAssessmentUser::where(['project_id' => $projectDataId, 'user_id' => $userData->id])->delete();

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function totalAssessedProjectsBasedOnProjectIds($projectIds)
    {
        try {
            $totalAssessedProjectsBasedOnProjectIds = ChallengeAssessmentUser::whereIn('project_id', $projectIds)->where('status', '1')->pluck('project_id');

            return $totalAssessedProjectsBasedOnProjectIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
