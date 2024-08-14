<?php

namespace App\Http\Resources\Project;

use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmittedProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skills = null;

        switch ($this->media_type) {
            case '0':
                $media = $this->media;
                break;
            case '1':
                $media = $this->getRawOriginal('media');
                break;
            case '2':
                $media = $this->media;
                break;
            default:
                $media = $this->media;
                break;
        }

        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_id'               => $this->user_id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'media_type'            => $this->media_type,
            'media'                 => $media,
            'skills'                => $skills,
            'updated_at'            => $this->updated_at,
        ];
    }
}
