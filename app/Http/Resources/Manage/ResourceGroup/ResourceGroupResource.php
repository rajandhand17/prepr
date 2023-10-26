<?php

namespace App\Http\Resources\Manage\ResourceGroup;

use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceModuleService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resourceModules = [];
        $achievements = [];
        $resource_collection = [];
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $tags = [];
        $tag_groups = [];
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $organization = null;
        $organization_id = null;
        $is_accessible = '';

        if ($this->resource_modules) {
            foreach ($this->resource_modules as $resource_module) {
                $resourceModules[$resource_module->resource_module_id]['uuid'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->uuid;
                $resourceModules[$resource_module->resource_module_id]['title'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->title;
                $resourceModules[$resource_module->resource_module_id]['image'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->media;
                $resourceModules[$resource_module->resource_module_id]['description'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->description;
            }
        }
        if ($this->getDuration) {
            $duration = $this->getDuration->title;
            $duration_id = $this->getDuration->id;
        }
        if ($this->getLevel) {
            $level = $this->getLevel->title;
            $level_id = $this->getLevel->id;
        }
        if ($this->getOrganization) {
            $organization = $this->getOrganization->title;
            $organization_id = $this->getOrganization->uuid;
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

        switch($this->privacy) {
            case '0':
                $privacy = 'yes';
                break;
            case '1':
                $privacy = 'no';
                break;
            default:
                $privacy = 'no';
                break;
        }

        switch($this->status) {
            case '0':
                $status = 'draft';
                break;
            case '1':
                $status = 'published';
                break;
            case '2':
                $status = 'archive';
                break;
            default:
                $status = 'draft';
                break;
        }

        if ($this->achievement) {
            $achievements = [
                'achievement_name'      => $this->achievement->achievement_name,
                'achievement_points'    => $this->achievement->achievement_points,
                'achievement_image'     => $this->achievement->achievement_image,
            ];
        }
        if ($this->resource_collection) {
            $resource_collection_id = $this->resource_collection->pluck('resource_collection_id');
            $resource_collection = ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection_id);
        }

        return [
            'id'                                       => $this->uuid,
            'language'                                 => $this->language,
            'title'                                    => $this->title,
            'slug'                                     => $this->slug,
            'description'                              => $this->description,
            'media_type'                               => $this->media_type,
            'cover_image'                              => $this->media,
            'privacy'                                  => $privacy,
            'status'                                   => $status,
            'is_accessible'                            => $is_accessible,
            'duration_id'                              => $duration_id,
            'duration'                                 => $duration,
            'level_id'                                 => $level_id,
            'level'                                    => $level,
            'resource_modules'                         => $resourceModules,
            'organization'                             => $organization,
            'organization_id'                          => $organization_id,
            'skills'                                   => $skills,
            'achievements'                             => $achievements,
            'skill_groups'                             => $skill_groups,
            'skill_stacks'                             => $skill_stacks,
            'tags'                                     => $tags,
            'tag_groups'                               => $tag_groups,
            'resource_collection'                      => $resource_collection,

        ];
    }
}
