<?php

namespace App\Http\Resources\Manage\LabProgram;

use App\Helpers\UtilityHelper;
use App\Services\Manage\LabService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $achievement = [];
        $componentAssociation = [];
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $tags = [];
        $tag_groups = [];
        $category = null;
        $category_id = null;
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $organization = null;
        $organization_id = null;
        $hosted_by = [];
        if ($this->component_association) {
            foreach ($this->component_association as $association) {
                if ($association->lab_id) {
                    $labData = LabService::getLabBasedOnId($association->lab_id);
                    if ($labData) {
                        $componentAssociation[$association->lab_id] = $labData;
                        $componentAssociation[$association->lab_id]['liked'] = $labData ? $labData->liked() : 'no';
                        $componentAssociation[$association->lab_id]['favourite'] = $labData ? $labData->favourite() : 'no';
                        $componentAssociation[$association->lab_id]['member_count'] = $labData ? $labData->members()->count() : 0;
                    }
                }
            }
        }
        if ($this->getOrganization) {
            $organization = $this->getOrganization->title;
            $organization_id = $this->getOrganization->uuid;
            $hosted_by = [
                'uuid'        => $this->getOrganization->uuid,
                'title'       => $this->getOrganization->title,
                'image'       => $this->getOrganization->image,
                'description' => $this->getOrganization->description,
                'slug'        => $this->getOrganization->slug,
            ];
        }
        if ($this->getCategory) {
            $category = $this->getCategory->title;
            $category_id = $this->getCategory->id;
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
        if ($this->tags) {
            $associatedSkillStacks = $this->tags->pluck('foreign_id');
            $tags = TagService::getTagsBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->tag_groups) {
            $associatedSkillStacks = $this->tag_groups->pluck('foreign_id');
            $tag_groups = TagGroupService::getTagGroupsBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->achievement) {
            $achievement = [
                'achievement_name'      => $this->achievement->achievement_name,
                'achievement_points'    => $this->achievement->achievement_points,
                'achievement_image'     => $this->achievement->achievement_image,
            ];
        }

        $module_status = 'not_started';
        $module_progress = [
            'status'        => $module_status,
            'percentage'    => '0',
        ];
        if ($this->lab_program_completion_status) {
            switch ($this->lab_program_completion_status->status) {
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
                'percentage'    => $this->lab_program_completion_status->percentage,
            ];
        }

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'title'                         => $this->title,
            'hosted_by'                     => $hosted_by,
            'slug'                          => $this->slug,
            'description'                   => $this->description,
            'labs'                          => $componentAssociation,
            'user_id'                       => $this->user_id,
            'media'                         => $this->media,
            'organization'                  => $organization,
            'organization_id'               => $organization_id,
            'category_id'                   => $category_id,
            'category'                      => $category,
            'duration_id'                   => $duration_id,
            'duration'                      => $duration,
            'level_id'                      => $level_id,
            'level'                         => $level,
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'tags'                          => $tags,
            'tag_groups'                    => $tag_groups,
            'achievement'                   => $achievement,
            'favourite'                     => $this->favourite(),
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'is_achievement_enabled'        => ($this->is_achievement_enabled == '1') ? 'yes' : 'no',
            'is_sequential'                 => ($this->is_sequential == '1') ? 'yes' : 'no',
            'is_accessible'                 => ($this->is_accessible == '1') ? 'yes' : 'no',
            'module_progress'               => $module_progress,
            'liked'                         => $this->liked(),
            'likes'                         => $this->likes()->count(),
            'shares'                        => $this->shares()->count(),
            'member_count'                  => '0', //Static for temporary basis,
            'last_updated'                  => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
