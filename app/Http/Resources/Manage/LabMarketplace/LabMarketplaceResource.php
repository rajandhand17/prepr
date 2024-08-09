<?php

namespace App\Http\Resources\Manage\LabMarketplace;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Manage\Challenge\ChallengeListNameResource;
use App\Http\Resources\Manage\ChallengePath\ChallengePathListNameResource;
use App\Http\Resources\Manage\Organization\OrganizationHostResource;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionListNameResource;
use App\Http\Resources\Manage\ResourceGroup\ResourceGroupListNameResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleListNameResource;
use App\Services\AchievementConditionListService;
use App\Services\Manage\ChallengePathTemplateService;
use App\Services\Manage\ChallengeTemplateService;
use App\Services\Manage\LabMarketplaceService;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\OrganizationService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class LabMarketplaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $address = [];
        $achievement = [];
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
        $challenges = [];
        $challenge_paths = [];
        $resource_modules = [];
        $resource_collections = [];
        $resource_groups = [];

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

        if ($this->address) {
            $address = [
                'latitude'  => $this->address->latitude,
                'longitude' => $this->address->longitude,
                'address'   => $this->address->address,
                'city'      => $this->address->city,
                'country'   => $this->address->country,
            ];
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

        $is_redeemed = 'yes';
        $organizationCheck = auth()->user()->preferred_organization;
        $organization = OrganizationService::getOrganizationExistBasedOnId($organizationCheck);
        $fetchOrganizationLimit = OrganizationService::OrganizationChargebeeLimit($organization);
        $labCredit = 'UnLimited';
        if ($fetchOrganizationLimit['lab_limit'] != 'UnLimited') {
            $labCredit = $fetchOrganizationLimit['lab_limit'] - $fetchOrganizationLimit['lab_count'];
        }
        $checkLabRedeem = LabMarketplaceService::checkLabRedeemedOrNot($this->id, $organization->id);
        if ($checkLabRedeem) {
            $is_redeemed = 'no';
        }

        if (!empty($this->component_association)) {
            foreach ($this->component_association as $lab_association) {
                if ($lab_association->challenge_id) {
                    $getChallengeTemplate = ChallengeTemplateService::getChallengeTemplateBasedOnId($lab_association->challenge_template_id);
                    $challenges[$lab_association->challenge_id] = ChallengeListNameResource::make($getChallengeTemplate);
                }

                if ($lab_association->challenge_path_id) {
                    $getChallengePath = ChallengePathTemplateService::getChallengePathBasedOnId($lab_association->challenge_path_id);
                    $challenge_paths[$lab_association->challenge_path_id] = ChallengePathListNameResource::make($getChallengePath);
                }

                if ($lab_association->resource_module_id) {
                    $getResourceModule = ResourceModuleService::getResourceModuleBasedOnId($lab_association->resource_module_id);
                    $resource_modules[$lab_association->resource_module_id] = ResourceModuleListNameResource::make($getResourceModule);
                }

                if ($lab_association->resource_collection_id) {
                    $getResourceCollection = ResourceCollectionService::getResourceCollectionBasedOnId($lab_association->resource_collection_id);
                    $resource_collections[$lab_association->resource_collection_id] = ResourceCollectionListNameResource::make($getResourceCollection);
                }

                if ($lab_association->resource_group_id) {
                    $getResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($lab_association->resource_group_id);
                    $resource_groups[$lab_association->resource_group_id] = ResourceGroupListNameResource::make($getResourceGroup);
                }
            }
        }
        return [
            'id'                            => $this->uuid,
            'type'                          => $type,
            'language'                      => $this->language,
            'user'                          => UserService::joinName($this->user->first_name, $this->user->last_name),
            'organization_id'               => $this->organization->uuid,
            'organization'                  => $this->organization->title,
            'hosted_by'                     => OrganizationHostResource::make($this->organization),
            'category_id'                   => $category_id,
            'category'                      => $category,
            'duration'                      => $duration,
            'duration_id'                   => $duration_id,
            'level'                         => $level,
            'level_id'                      => $level_id,
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'description'                   => $this->description,
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'media_type'                    => $this->media_type,
            'media'                         => $media,
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'is_auto_created'               => ($this->is_auto_created == '1') ? 'yes' : 'no',
            'is_resource_sequential'        => ($this->is_resource_sequential == '1') ? 'yes' : 'no',
            'is_sequential'                 => ($this->is_sequential == '1') ? 'yes' : 'no',
            'is_achievement_enabled'        => ($this->is_achievement_enabled == '1') ? 'yes' : 'no',
            'is_notification_enabled'       => ($this->is_notification_enabled == '1') ? 'yes' : 'no',
            'is_verified'                   => ($this->is_verified == '1') ? 'yes' : 'no',
            'address'                       => $address,
            'achievement'                   => $achievement,
            'external_links'                => LabMarketplaceExternalLinkResource::collection($this->external_links),
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'tags'                          => $tags,
            'tag_groups'                    => $tag_groups,
            'challenge_count'               => count($challenges),
            'challenge_path_count'          => count($challenge_paths),
            'resource_module_count'         => count($resource_modules),
            'resource_collection_count'     => count($resource_collections),
            'resource_group_count'          => count($resource_groups),
            'challenge'                     => $challenges,
            'challenge_path'                => $challenge_paths,
            'resource_module'               => $resource_modules,
            'resource_collection'           => $resource_collections,
            'resource_group'                => $resource_groups,
            'last_updated'                  => UtilityHelper::formatDateTime($this->updated_at),
            'is_redeemed'                   => $is_redeemed,
            'credit_score'                  => $labCredit,
        ];
    }
}
