<?php

namespace App\Http\Resources\Public\Lab;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Public\Airmeet\AirmeetEventResource;
use App\Http\Resources\Public\Challenge\ChallengeListNameResource;
use App\Http\Resources\Public\ChallengePath\ChallengePathListNameResource;
use App\Http\Resources\Public\LabProgram\LabProgramListNameResource;
use App\Http\Resources\Public\Organization\OrganizationHostResource;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionListNameResource;
use App\Http\Resources\Public\ResourceGroup\ResourceGroupListNameResource;
use App\Http\Resources\Public\ResourceModule\ResourceModuleListNameResource;
use App\Services\AchievementConditionListService;
use App\Services\Public\ChallengePathService;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabProgramService;
use App\Services\Public\ResourceCollectionService;
use App\Services\Public\ResourceGroupService;
use App\Services\Public\ResourceModuleService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
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
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $tags = [];
        $tag_groups = [];
        $achievement = [];
        $lab_programs = [];
        $challenges = [];
        $challenge_paths = [];
        $resource_modules = [];
        $resource_collections = [];
        $resource_groups = [];
        $module_progress = null;

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

        if ($this->tags) {
            $associatedSkillStacks = $this->tags->pluck('foreign_id');
            $tags = TagService::getTagsBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->tag_groups) {
            $associatedSkillStacks = $this->tag_groups->pluck('foreign_id');
            $tag_groups = TagGroupService::getTagGroupsBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->achievement) {
            $achievement_conditions = [];
            foreach ($this->achievement->achievement_condition as $achievement_condition) {
                $check_achievement_condition = AchievementConditionListService::getAchievementConditionByID($this->language, $achievement_condition);
                $achievement_conditions[$check_achievement_condition->id] = $check_achievement_condition->title;
            }
            $achievement = [
                'achievement_name'      => $this->achievement->achievement_name,
                'achievement_points'    => $this->achievement->achievement_points,
                'achievement_image'     => $this->achievement->achievement_image,
                'achievement_condition' => json_decode(json_encode($achievement_conditions)),
            ];
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

        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
                break;
            default:
                $media = $this->media;
                break;
        }

        if (!empty($this->component_association)) {
            foreach ($this->component_association as $lab_association) {
                if (count($lab_programs) < 5) {
                    if ($lab_association->lab_program_id) {
                        $getLabProgram = LabProgramService::getLabProgramBasedOnId($lab_association->lab_program_id);
                        if ($getLabProgram !== null) {
                            $lab_programs[$lab_association->lab_program_id] = LabProgramListNameResource::make($getLabProgram);
                        }
                    }
                }
                if (count($challenges) < 5) {
                    if ($lab_association->challenge_id) {
                        $getChallenge = ChallengeService::getChallengeBasedOnId($lab_association->challenge_id);
                        if ($getChallenge !== null) {
                            $challenges[$lab_association->challenge_id] = ChallengeListNameResource::make($getChallenge);
                        }
                    }
                }
                if (count($challenge_paths) < 5) {
                    if ($lab_association->challenge_path_id) {
                        $getChallengePath = ChallengePathService::getChallengePathBasedOnId($lab_association->challenge_path_id);
                        if ($getChallengePath !== null) {
                            $challenge_paths[$lab_association->challenge_path_id] = ChallengePathListNameResource::make($getChallengePath);
                        }
                    }
                }
                if (count($resource_modules) < 5) {
                    if ($lab_association->resource_module_id) {
                        $getResourceModule = ResourceModuleService::getResourceModuleBasedOnId($lab_association->resource_module_id);
                        if ($getResourceModule !== null) {
                            $resource_modules[$lab_association->resource_module_id] = ResourceModuleListNameResource::make($getResourceModule);
                        }
                    }
                }
                if (count($resource_collections) < 5) {
                    if ($lab_association->resource_collection_id) {
                        $getResourceCollection = ResourceCollectionService::getResourceCollectionBasedOnId($lab_association->resource_collection_id);
                        if ($getResourceCollection !== null) {
                            $resource_collections[$lab_association->resource_collection_id] = ResourceCollectionListNameResource::make($getResourceCollection);
                        }
                    }
                }
                if (count($resource_groups) < 5) {
                    if ($lab_association->resource_group_id) {
                        $getResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($lab_association->resource_group_id);
                        if ($getResourceGroup !== null) {
                            $resource_groups[$lab_association->resource_group_id] = ResourceGroupListNameResource::make($getResourceGroup);
                        }
                    }
                }
            }
        }

        $type = 'na';

        switch ($this->type) {
            case '0':
                $type = 'assess';
                break;
            case '1':
                $type = 'onboard';
                break;
            case '2':
                $type = 'engage';
                break;
            case '3':
                $type = 'grow';
                break;
            default:
                $type = 'na';
                break;
        }

        if (auth('api')->check()) {
            $module_status = 'not_started';
            $module_progress = [
                'status'        => $module_status,
                'percentage'    => '0',
            ];
            if ($this->lab_completion_status) {
                switch ($this->lab_completion_status->status) {
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
                    'percentage'    => $this->lab_completion_status->percentage,
                ];
            }
        }

        return [
            'id'                            => $this->uuid,
            'type'                          => $type,
            'language'                      => $this->language,
            'is_pre_build'                  => ($this->is_pre_built == '1' ? 'yes' : 'no'),
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'description'                   => $this->description,
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'media_type'                    => $this->media_type,
            'media'                         => $media,
            'hosted_by'                     => OrganizationHostResource::make($this->organization),
            'category_id'                   => $category_id,
            'category'                      => $category,
            'organization_id'               => isset($this->organization->uuid) ? $this->organization->uuid : null,
            'organization'                  => isset($this->organization->title) ? $this->organization->title : null,
            'duration'                      => $duration,
            'duration_id'                   => $duration_id,
            'level'                         => $level,
            'level_id'                      => $level_id,
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'member_count'                  => $this->members()->count(),
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'tags'                          => $tags,
            'tag_groups'                    => $tag_groups,
            'likes'                         => $this->likes()->count(),
            'shares'                        => $this->shares()->count(),
            'joined'                        => $join_status,
            'is_live_event_enabled'         => $this->is_live_event_enabled ? 'yes' : 'no',
            'live_event'                    => AirmeetEventResource::make($this->airmeet),
            'liked'                         => $this->liked(),
            'favourite'                     => $this->favourite(),
            'is_accessible'                 => ($this->is_accessible == '1') ? 'yes' : 'no',
            'module_progress'               => $module_progress,
            'lab_address'                   => LabAddressResource::make($this->address),
            'lab_achievement'               => $achievement,
            'lab_external_links'            => LabExternalLinksResource::collection($this->external_links),
            'lab_program_count'             => count($lab_programs),
            'challenge_count'               => count($challenges),
            'challenge_path_count'          => count($challenge_paths),
            'resource_module_count'         => count($resource_modules),
            'resource_collection_count'     => count($resource_collections),
            'resource_group_count'          => count($resource_groups),
            'lab_program'                   => $lab_programs,
            'challenge'                     => $challenges,
            'challenge_path'                => $challenge_paths,
            'resource_module'               => $resource_modules,
            'resource_collection'           => $resource_collections,
            'resource_group'                => $resource_groups,
            'last_updated'                  => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
