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
        $flexible_date = [];

        if ($this->challenge_timelines?->timeline_type == '0') {
            $flexible_date = [
                'timeline_type'                 => 'flexible',
                'flexible_date_number'          => $this->challenge_timelines?->flexible_date_number,
                'flexible_date_duration'        => $this->challenge_timelines?->flexible_date_duration,
                'automatic_alert'               => $this->challenge_timelines?->automatic_alert == '0' ? 'day' : 'week',
                'flexible_expire_deadline'      => $this->challenge_timelines?->flexible_expire_deadline,
            ];
        } elseif ($this->challenge_timelines?->timeline_type == '1') {
            $flexible_date = [
                'timeline_type'                          => 'restricted',
                'start_date'                             => $this->challenge_timelines?->start_date,
                'start_date_description'                 => $this->challenge_timelines?->start_date_description,
                'registration_deadline_date'             => $this->challenge_timelines?->registration_deadline_date,
                'registration_deadline_date_description' => $this->challenge_timelines?->registration_deadline_date_description,
                'submission_deadline_date'               => $this->challenge_timelines?->submission_deadline_date,
                'submission_deadline_date_description'   => $this->challenge_timelines?->submission_deadline_date_description,
                'challenge_duration'                     => $this->challenge_timelines?->challenge_duration,
            ];
        }

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
            'is_pre_build'       => ($this->is_pre_built == '1' ? 'yes' : 'no'),
            'flexible_date'     => $flexible_date,
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
