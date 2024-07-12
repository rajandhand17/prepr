<?php

namespace App\Http\Resources\Manage\ResourceCollection;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ResourceModuleService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $resourceModules = [];
        $labs = [];
        $challenges = [];
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
        $module_progress = null;
        if (count($this->resource_modules) > 0) {
            foreach ($this->resource_modules as $resource_module) {
                $resourceModuleData = ResourceModuleService::getResourceModuleBasedOnId($resource_module->resource_module_id);
                if ($resourceModuleData !== null) {
                    $resourceModules[$resource_module->resource_module_id]['uuid'] = $resourceModuleData->uuid;
                    $resourceModules[$resource_module->resource_module_id]['title'] = $resourceModuleData->title;
                    $resourceModules[$resource_module->resource_module_id]['image'] = $resourceModuleData->media;
                    $resourceModules[$resource_module->resource_module_id]['description'] = $resourceModuleData->description;
                    $resourceModules[$resource_module->resource_module_id]['slug'] = $resourceModuleData->slug;
                }
            }
        }
        if ($this->challenges) {
            foreach ($this->challenges as $challenge_records) {
                if (!isset(ChallengeService::getChallengeBasedOnId($challenge_records->challenge_id)->uuid)) {
                    continue;
                }
                $challenges[$challenge_records->challenge_id]['uuid'] = ChallengeService::getChallengeBasedOnId($challenge_records->challenge_id)->uuid;
                $challenges[$challenge_records->challenge_id]['title'] = ChallengeService::getChallengeBasedOnId($challenge_records->challenge_id)->title;
                $challenges[$challenge_records->challenge_id]['image'] = ChallengeService::getChallengeBasedOnId($challenge_records->challenge_id)->image;
                $challenges[$challenge_records->challenge_id]['description'] = ChallengeService::getChallengeBasedOnId($challenge_records->challenge_id)->description;
                $challenges[$challenge_records->challenge_id]['slug'] = ChallengeService::getChallengeBasedOnId($challenge_records->challenge_id)->slug;
            }
        }
        if ($this->labs) {
            foreach ($this->labs as $lab_records) {
                if (!isset(LabService::getLabBasedOnId($lab_records->lab_id)->uuid)) {
                    continue;
                }
                $labs[$lab_records->lab_id]['uuid'] = LabService::getLabBasedOnId($lab_records->lab_id)->uuid;
                $labs[$lab_records->lab_id]['title'] = LabService::getLabBasedOnId($lab_records->lab_id)->title;
                $labs[$lab_records->lab_id]['image'] = LabService::getLabBasedOnId($lab_records->lab_id)->media;
                $labs[$lab_records->lab_id]['description'] = LabService::getLabBasedOnId($lab_records->lab_id)->description;
                $labs[$lab_records->lab_id]['slug'] = LabService::getLabBasedOnId($lab_records->lab_id)->slug;
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
                $privacy = 'no';
                break;
            case '1':
                $privacy = 'yes';
                break;
            default:
                $privacy = 'nan';
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

        $rating = intval('0');
        if ($this->resource_rating) {
            $rating = intval($this->resource_rating->rating);
        }

        if ($this->resource_collection_completion_status) {
            switch ($this->resource_collection_completion_status->status) {
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
                'percentage'    => $this->resource_collection_completion_status->percentage,
            ];
        }

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'description'                   => $this->description,
            'media_type'                    => $this->media_type,
            'cover_image'                   => $this->media,
            'privacy'                       => $privacy,
            'status'                        => $status,
            'duration_id'                   => $duration_id,
            'duration'                      => $duration,
            'level_id'                      => $level_id,
            'level'                         => $level,
            'resource_modules'              => $resourceModules,
            'labs'                          => $labs,
            'challenges'                    => $challenges,
            'organization'                  => $organization,
            'organization_id'               => $organization_id,
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'tags'                          => $tags,
            'tag_groups'                    => $tag_groups,
            'rating'                        => $rating,
            'liked'                         => $this->liked(),
            'module_progress'               => $module_progress,
            'is_accessible'                 => ($this->is_accessible == '1') ? 'yes' : 'no',
        ];
    }
}
