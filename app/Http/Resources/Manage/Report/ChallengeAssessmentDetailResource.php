<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAssessmentDetailResource extends JsonResource
{
    private $user_score = 0;
    private $user_weight = 0;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $challengeAssessments = $this->challengeAssessmentUsers;

        return [
            'id'                => $this->id,
            'full_name'         => $this->full_name,
            'profile'           => $this->profile_image,
            'comments'          => '',
            'criteria_comment'  => $challengeAssessments->first()->criteria_comment,
            'assessments'       => $this->formatAssessments($challengeAssessments),
            'score'             => $this->user_score,
            'weight'            => $this->user_weight,
        ];
    }

    private function formatAssessments($challengeAssessments)
    {
        $this->user_score = 0;
        $this->user_weight = 0;

        return $challengeAssessments->map(function ($challengeAssessment) {
            $challengeAssessmentCriteria = $challengeAssessment->challengeAssessmentCriteria;

            if (is_null($challengeAssessmentCriteria?->title)) {
                return null;
            } else {
                $this->user_score += $challengeAssessment?->score ?? 0;
                $this->user_weight += $challengeAssessmentCriteria?->weight ?? 0;
            }

            return [
                'id'             => $challengeAssessment->id,
                'criteria'       => $challengeAssessmentCriteria?->title,
                'weight'         => $challengeAssessmentCriteria?->weight,
                'score'          => $challengeAssessmentCriteria?->score,
                'score_received' => $challengeAssessment->score,
                'comment'        => $challengeAssessment->comment,
            ];
        })->filter()->toArray();
    }
}
