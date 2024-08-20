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
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'challenge'   => [
                'slug'  => $this->challenge->slug,
                'title' => $this->challenge->title,
            ],
        ];
    }
}
