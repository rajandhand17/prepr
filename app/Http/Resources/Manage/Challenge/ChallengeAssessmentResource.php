<?php

namespace App\Http\Resources\Manage\Challenge;

use App\Services\Manage\ChallengeAssessmentService;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayal|\JsonSerializable
     */
    public function toArray($request)
    {
        $challenge_assessment_criteria = null;
        $challenge_assessment = null;

        if ($this->challenge_assessment_criteria) {
            $challenge_assessment_criteria = $this->challenge_assessment_criteria->map(function ($item) {
                return [
                    'assessment_title'        => $item->title,
                    'assessment_description'  => $item->description,
                    'assessment_score'        => $item->score,
                    'assessment_weight'       => $item->weight,
                ];
            });
        }

        if ($this->challenge_assessment) {
            $challenge_assessment = ChallengeAssessmentService::getChallengeAssessmentData($this->challenge_assessment);
        }

        return [
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'project_assessed_count'        => '0', // Till project api's are not done statically sending this
            'challenge_assessment_criteria' => $challenge_assessment_criteria,
            'challenge_assessment'          => $challenge_assessment,
        ];
    }
}
