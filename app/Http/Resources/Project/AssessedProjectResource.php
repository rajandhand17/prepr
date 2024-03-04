<?php

namespace App\Http\Resources\Project;

use App\Services\ChallengeAssessmentUserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessedProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request): array
    {
        $project_assessment = null;
        $assessmentStatus = 'pending';
        $assessmentOverAllComment = null;

        if ($this->getProjectAssessment) {
            $project_assessment = $this->getProjectAssessment->getAssessmentCriterias->map(function ($criteria) {
                $criteriaData = ChallengeAssessmentUserService::getcriteriaDataBasedOnId($criteria, $this->id);

                return [
                    'id'                => $criteriaData->id,
                    'title'             => $criteriaData->title,
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
                    $assessmentStatus = 'draft';
                    break;

                case false:
                    $assessmentStatus = 'publish';
                    break;

                default:
                    $assessmentStatus = 'pending';
                    break;
            }
        }

        if ($project_assessment != null && $project_assessment->isNotEmpty()) {
            $assessmentComment = $project_assessment->pluck('criteria_comment')->unique();
            $assessmentOverAllComment = $assessmentComment[0];
        }

        return [
            'assessmentStatus'          => $assessmentStatus,
            'assessmentOverAllComment'  => $assessmentOverAllComment,
            'assessmentScoringData'     => $project_assessment,
        ];
    }
}
