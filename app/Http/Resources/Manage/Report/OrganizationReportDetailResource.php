<?php

namespace App\Http\Resources\Manage\Report;

use App\Http\Resources\Manage\Organization\OrganizationAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationReportDetailResource extends JsonResource
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
            'address'                    => OrganizationAddressResource::collection($this->address),
            'created_at'                 => $this->created_at,
            'members_count'              => $this->whenCounted('members'),
            'challenges_count'           => $this->challenges_count_count,
            'labs_count'                 => $this->labs_count_count,
            'resource_modules_count'     => $this->resource_modules_count_count,
            'challenge_paths_count'      => $this->challenge_paths_count_count,
            'lab_programs_count'         => $this->lab_programs_count_count,
            'resource_collections_count' => $this->resource_collections_count_count,
            'resource_groups_count'      => $this->resource_groups_count_count,
        ];
    }
}
