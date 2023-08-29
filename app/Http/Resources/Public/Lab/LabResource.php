<?php

namespace App\Http\Resources\Public\Lab;

use App\Helpers\UtilityHelper;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
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
        $category_id = null;
        $category = null;
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;

        if ($this->getCategory) {
            $category_id = $this->getCategory->id;
            $category = $this->getCategory->title;
        }

        if ($this->durations) {
            $duration = $this->durations->title;
            $duration_id = $this->durations->id;
        }

        if ($this->levels) {
            $level = $this->levels->title;
            $level_id = $this->levels->id;
        }

        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('foreign_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        if ($this->skill_groups) {
            $associatedSkillGroups = $this->skill_groups->pluck('foreign_id');
            $skill_groups = SkillGroupService::getSkillGroupsBasedOnIds($associatedSkillGroups)->pluck('title', 'id');

            if ($skill_groups->isEmpty()) {
                $skill_groups = $this->skill_groups->pluck('foreign_id');
            }
        }

        if ($this->skill_stacks) {
            $associatedSkillStacks = $this->skill_stacks->pluck('foreign_id');
            $skill_stacks = SkillStackService::getSkillStacksBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        $joined_status = $this->joined();
        $join_status = 'No';
        if ($joined_status != 'NA' && $joined_status != null) {
            switch ($joined_status->invite_status) {
                case '0':
                    $join_status = 'Invited';
                    break;
                case '1':
                    $join_status = 'Yes';
                    break;
                case '2':
                    $join_status = 'Pending';
                    break;
                case '3':
                    $join_status = 'No';
                    break;
                default:
                    $join_status = 'No';
                    break;
            }
        }

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'description'                   => $this->description,
            'privacy'                       => $this->type,
            'media_type'                    => $this->media_type,
            'media'                         => $this->media,
            'category_id'                   => $category_id,
            'category'                      => $category,
            'organization_id'               => $this->organization->uuid,
            'organization'                  => $this->organization->title,
            'duration'                      => $duration,
            'duration_id'                   => $duration_id,
            'level'                         => $level,
            'level_id'                      => $level_id,
            'status'                        => $this->status,
            'member_count'                  => $this->members()->count(),
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'likes'                         => $this->likes()->count(),
            'shares'                        => $this->shares()->count(),
            'joined'                        => $join_status,
            'liked'                         => $this->liked(),
            'favourite'                     => $this->favourite(),
            'lab_address'                   => LabAddressResource::make($this->address),
            'lab_achievement'               => LabAchievementResource::make($this->achievement),
            'lab_external_links'            => LabExternalLinksResource::collection($this->external_links),
            'last_updated'                  => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
