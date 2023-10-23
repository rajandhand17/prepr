<?php

namespace App\Http\Resources\Manage\ResourceCollection;

use App\Models\Challenge;
use App\Models\ResourceCollection;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceModuleService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Illuminate\Events\queueable;

class ResourceCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {

        $componentAssociation = [];
        $resourceModule = [];
        $lab=[];
        $challenge=[];
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
        if ($this->component_association) {
            foreach ($this->component_association as $association) {
                if(!empty($association->resource_module_id)){
                    $resourceModule[$association->resource_module_id] = ResourceModuleService::getResourceModuleBasedOnId($association->resource_module_id);
                    $resourceModule[$association->resource_module_id]['title'] = ResourceModuleService::getResourceModuleBasedOnId($association->resource_module_id)->title;
                    $resourceModule[$association->resource_module_id]['description'] = ResourceModuleService::getResourceModuleBasedOnId($association->resource_module_id)->description;
                }
                if(!empty($association->lab_id)){
                    $lab[$association->lab_id] = LabService::getLabBasedOnId($association->lab_id);
                    $lab[$association->lab_id]['title'] = LabService::getLabBasedOnId($association->lab_id)->title;
                    $lab[$association->lab_id]['description'] = LabService::getLabBasedOnId($association->lab_id)->description;
                }
                if(!empty($association->challenge_id)){
                    $challenge[$association->challenge_id] =ChallengeService::getChallengeBasedOnId($association->challenge_id);
                    $challenge[$association->challenge_id]['title'] = ChallengeService::getChallengeBasedOnId($association->challenge_id)->title;
                    $challenge[$association->challenge_id]['description'] = ChallengeService::getChallengeBasedOnId($association->challenge_id)->description;
                }
            }
        }

        if ($this->durations) {
            $duration = $this->durations->title;
            $duration_id = $this->durations->id;
        }
        if ($this->levels) {
            $level = $this->levels->title;
            $level_id = $this->levels->id;
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
        return [
             'id'                                     => $this->uuid,
            'language'                                => $this->language,
            'title'                                   => $this->title,
            'slug'                                    => $this->slug,
            'description'                             => $this->description,
            'media_type'                              => $this->media_type,
            'cover_image'                             => $this->media,
            'privacy'                                 => $privacy,
            'status'                                  => $status,
            'is_accessible'                           => $is_accessible,
            'duration_id'                             => $duration_id,
            'resource_module'                         => $resourceModule,
            'duration'                                => $duration,
            'level_id'                                => $level_id,
            'lab'                                     => $lab,
            'challenge'                               => $challenge,
            'level'                                   => $level,
            'organization'                            => $organization,
            'organization_id'                         => $organization_id,
            'skills'                                  => $skills,
            'skill_groups'                            => $skill_groups,
            'skill_stacks'                            => $skill_stacks,
            'tags'                                    => $tags,
            'tag_groups'                              => $tag_groups,

        ];
    }
}
