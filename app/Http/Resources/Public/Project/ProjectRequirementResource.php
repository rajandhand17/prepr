<?php

namespace App\Http\Resources\Public\Project;

use App\Services\Manage\ProjectService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectRequirementResource extends JsonResource
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
        $projectRequirements = ProjectService::projectRequirements($this);

        return $projectRequirements;
    }
}
