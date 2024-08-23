<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabReportDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'                       => $this->uuid,
            'title'                      => $this->title,
            'slug'                       => $this->slug,
            'user_id'                    => $this->user_id,
            'duration'                   => $this->formatted_lab_duration,
            'level'                      => $this->formatted_lab_level,
            'type'                       => $this->type,
            'privacy'                    => $this->formatted_lab_privacy,
            'members_count'              => $this->whenCounted('members'),
            'challenges_count'           => $this->whenCounted('challenges'),
            'challenge_paths_count'      => $this->whenCounted('challengePaths'),
            'resource_modules_count'     => $this->whenCounted('resourceModules'),
            'resource_collections_count' => $this->whenCounted('resourceCollections'),
            'resource_groups_count'      => $this->whenCounted('resourceGroups'),
        ];
    }
}
