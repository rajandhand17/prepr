<?php

namespace App\Http\Resources\manage;

use App\Http\Resources\Manage\MemberManagement\MemberManagementResource;
use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengePathAssociatedChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skills = null;
        $module_status = 'not_started';
        $module_progress = [
            'status'        => $module_status,
            'percentage'    => '0',
        ];
        if ($this->challenge_completion_status) {
            switch ($this->challenge_completion_status->status) {
                case '0':
                    $module_status = 'not_started';
                    break;
                case '1':
                    $module_status = 'in_progress';
                    break;
                case '2':
                    $module_status = 'completed';
                    break;
            }

            $module_progress = [
                'status'        => $module_status,
                'percentage'    => $this->challenge_completion_status->percentage,
            ];
        }

        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
                break;
            default:
                $media = $this->media;
                break;
        }

        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('foreign_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        return [
            'id'                                => $this->id,
            'uuid'                              => $this->uuid,
            'slug'                              => $this->slug,
            'title'                             => $this->title,
            'description'                       => $this->description,
            'status'                            => ($this->status == '0') ? 'draft' : 'published',
            'media'                             => $media,
            'module_progress'                   => $module_progress,
            'submissions_count'                 => $this->submitted_projects()->count(),
            'members_count'                     => $this->members()->count(),
            'members'                           => MemberManagementResource::collection($this->members),
            'durations'                         => $this->durations?->title,
            'level'                             => $this->levels?->title,
            'liked'                             => $this->liked(),
            'favourite'                         => $this->favourite(),
            'skills'                            => $skills,
        ];
    }
}
