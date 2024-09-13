<?php

namespace App\Http\Resources\Manage\Report\Components;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeReportDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $submission_date = $this->formatted_submission_deadline_date;

        return [
            'uuid'               => $this->uuid,
            'title'              => $this->title,
            'slug'               => $this->slug,
            'user_id'            => $this->user_id,
            'submission_date'    => $submission_date !== 'N/A' ? Carbon::parse($submission_date)->toIso8601String() : $submission_date,
            'duration'           => $this->formatted_challenge_duration,
            'level'              => $this->formatted_challenge_level,
            'privacy'            => $this->formatted_challenge_privacy,
            'members_count'      => $this->whenCounted('members'),
            'sponsors'           => $this->whenCounted('hosts'),
            'submitted_projects' => $this->whenCounted('submitted_projects'),
            'component_counts'   => [
                'labs_count'                 => $this->whenCounted('labs'),
                'lab_programs_count'         => $this->whenCounted('labPrograms'),
                'resource_modules_count'     => $this->whenCounted('resourceModules'),
                'resource_collections_count' => $this->whenCounted('resourceCollections'),
                'resource_groups_count'      => $this->whenCounted('resourceGroups'),
            ],
        ];
    }
}
