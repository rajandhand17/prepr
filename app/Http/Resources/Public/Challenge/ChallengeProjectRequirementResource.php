<?php

namespace App\Http\Resources\Public\Challenge;

use App\Services\Public\ChallengeService;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeProjectRequirementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $projectRequirements = ChallengeService::getProjectChallengeRequirement($this);

        return $projectRequirements;
    }
}
