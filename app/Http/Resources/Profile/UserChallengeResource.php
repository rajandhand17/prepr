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

class UserChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          // Format the response data
          $formattedChallenges = $this->map(function ($challenge) {
            return [
                'id'                        => $challenge->id,
                'title'                     => $challenge->title,
                'description'               => $challenge->description,
                'slug'                      => $challenge->slug,
                'media'                     => $challenge->media,
                'privacy'                   => $challenge->privacy,
                'media_type'                => $challenge->media_type,
                'challenge_timelines'       => $challenge->challenge_timelines,
                'participation_achievement' => $challenge->participation_achievement,
            ];
        });
        return [
            'data'       => $formattedChallenges,
            'pagination' => [
                'current_page' => $this->currentPage(),
                'per_page'     => $this->perPage(),
                'total'        => $this->total(),
                'last_page'    => $this->lastPage(),
            ],
        ];
    }
}
