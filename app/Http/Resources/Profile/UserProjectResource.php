<?php

namespace App\Http\Resources\Profile;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\ProjectPitchService;
use App\Services\ProjectService;
use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Format the response data
        $formattedProjects = $this->map(function ($project) {
            return [
                'id'          => $project->id,
                'title'       => $project->title,
                'description' => $project->description,
                'challenge'   => [
                    'slug'  => $project->challenge->slug,
                    'title' => $project->challenge->title,
                ],
            ];
        });
        return  [
            'data'       => $formattedProjects,
            'pagination' => [
                'current_page' => $this->currentPage(),
                'per_page'     => $this->perPage(),
                'total'        => $this->total(),
                'last_page'    => $this->lastPage(),
            ],
        ];
    }
}
