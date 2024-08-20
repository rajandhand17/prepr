<?php

namespace App\Http\Resources\Public\ResourceGroup;

use App\Http\Resources\Public\Organization\OrganizationHostResource;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceModuleService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $resourceModules = [];
        $achievements = [];
        $resourceCollection = [];
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $organization = null;
        $organization_id = null;
        $module_progress = null;
        $category = null;
        $category_id = null;

        if ($this->getDuration) {
            $duration = $this->getDuration->title;
            $duration_id = $this->getDuration->id;
        }
        if ($this->getLevel) {
            $level = $this->getLevel->title;
            $level_id = $this->getLevel->id;
        }
        if ($this->getCategory) {
            $category = $this->getCategory->title;
            $category_id = $this->getCategory->id;
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

        if ($this->achievement) {
            $achievements = [
                'achievement_name'      => $this->achievement->achievement_name,
                'achievement_points'    => $this->achievement->achievement_points,
                'achievement_image'     => $this->achievement->achievement_image,
            ];
        }
        if ($this->resource_modules) {
            foreach ($this->resource_modules as $resource_module) {
                if (ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id) == '') {
                    continue;
                }
                $resourceModules[$resource_module->resource_module_id]['uuid'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->uuid;
                $resourceModules[$resource_module->resource_module_id]['title'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->title;
                $resourceModules[$resource_module->resource_module_id]['image'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->media;
                $resourceModules[$resource_module->resource_module_id]['description'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->description;
                $resourceModules[$resource_module->resource_module_id]['slug'] = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id)->slug;
            }
        }
        if ($this->resource_collection) {
            foreach ($this->resource_collection as  $resource_collection) {
                if (ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection->resource_collection_id) == '') {
                    continue;
                }
                $resourceCollection[$resource_collection->resource_collection_id]['uuid'] = ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection->resource_collection_id)->uuid;
                $resourceCollection[$resource_collection->resource_collection_id]['title'] = ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection->resource_collection_id)->title;
                $resourceCollection[$resource_collection->resource_collection_id]['image'] = ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection->resource_collection_id)->media;
                $resourceCollection[$resource_collection->resource_collection_id]['description'] = ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection->resource_collection_id)->description;
                $resourceCollection[$resource_collection->resource_collection_id]['slug'] = ResourceCollectionService::getResourceCollectionBasedOnId($resource_collection->resource_collection_id)->slug;
            }
        }
        $rating = intval('0');
        if (auth('api')->check()) {
            if ($this->resource_rating) {
                $rating = intval($this->resource_rating->rating);
            }

            $module_status = 'not_started';
            $module_progress = [
                'status'        => $module_status,
                'percentage'    => '0',
            ];
            if ($this->resource_group_completion_status) {
                switch ($this->resource_group_completion_status->status) {
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
                    'percentage'    => $this->resource_group_completion_status->percentage,
                ];
            }
        }
        $type = $this->resource_group_type->map(function ($item) {
            return config('constants.resource_types_key.'.$item->value);
        });

        $mode = $this->resource_group_mode->map(function ($item) {
            return config('constants.resource_mode_type_key.'.$item->value);
        });

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'hosted_by'                     => OrganizationHostResource::make($this->getOrganization),
            'description'                   => $this->description,
            'media_type'                    => $this->media_type,
            'cover_image'                   => $this->media,
            'privacy'                       => ($this->privacy == '1') ? 'private' : 'public',
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'duration_id'                   => $duration_id,
            'duration'                      => $duration,
            'category'                      => $category,
            'category_id'                   => $category_id,
            'level_id'                      => $level_id,
            'level'                         => $level,
            'resource_modules'              => $resourceModules,
            'organization'                  => $organization,
            'organization_id'               => $organization_id,
            'skills'                        => $skills,
            'achievement'                   => $achievements,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'favourite'                     => $this->favourite(),
            'liked'                         => $this->liked(),
            'like_count'                    => $this->liked_count(),
            'is_accessible'                 => ($this->is_accessible == '1') ? 'yes' : 'no',
            'rating'                        => $rating,
            'module_progress'               => $module_progress,
            'resource_collection'           => $resourceCollection,
            'mode'                          => $mode,
            'type'                          => $type,
        ];
    }
}
