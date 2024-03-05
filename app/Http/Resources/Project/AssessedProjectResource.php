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
        $fetchProjectAssessment = ChallengeAssessmentUserService::getProjectAssessmentData($this);

        return $fetchProjectAssessment;
    }
}
