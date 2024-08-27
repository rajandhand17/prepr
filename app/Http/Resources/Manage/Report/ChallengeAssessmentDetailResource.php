<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAssessmentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $challengeAssessments = $this->challengeAssessmentUsers;

        return [
            'id'               => $this->id,
            'full_name'        => $this->full_name,
            'profile'          => $this->profile_image,
            'comments'         => '',
            'criteria_comment' => $challengeAssessments->first()->criteria_comment,
            'assessments'      => $this->formatAssessments($challengeAssessments),
        ];
    }

    private function formatAssessments($challengeAssessments)
    {
        return $challengeAssessments->map(function ($challengeAssessment) {
            $challengeAssessmentCriteria = $challengeAssessment->challengeAssessmentCriteria;

            return [
                'id'             => $challengeAssessment->id,
                'criteria'       => $challengeAssessmentCriteria->title,
                'weight'         => $challengeAssessmentCriteria->weight,
                'score'          => $challengeAssessmentCriteria->score,
                'score_received' => $challengeAssessment->score,
                'comment'        => $challengeAssessment->comment,
            ];
        });
    }
}
