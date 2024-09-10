<?php

namespace App\Http\Resources\Manage\Lab;

use App\Http\Resources\Manage\Airmeet\AirmeetEventResource;
use App\Http\Resources\Manage\Organization\OrganizationHostResource;
use App\Services\AchievementConditionListService;
use App\Services\Manage\LabService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\UserService;
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
        $address = [];
        $achievement = [];
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $category = null;
        $category_id = null;
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;

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
                if ($check_achievement_condition !== null) {
                    $achievement_conditions[$check_achievement_condition->id] = $check_achievement_condition->title;
                }
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
        if ($media == config('site-settings.aws_url').config('site-settings.default_lab_cover_image') || $media == config('site-settings.aws_url')) {
            $media = null;
        }
        $campusConnectOpportunity = in_array($this->campus_connect_status, ['both', 'job']) ? data_get($this, 'campusConnectOpportunity.metadata') : null;
        $campusConnectStory = in_array($this->campus_connect_status, ['both', 'story']) ? data_get($this, 'campusConnectStory.metadata') : null;

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
        $created_by = [];
        if (!empty($this->user_id)) {
            $userDetails = UserService::getUserById($this->user_id);
            $created_by['uuid'] = $userDetails->uuid;
            $created_by['full_name'] = $userDetails->full_name;
            $created_by['username'] = $userDetails->username;
            $created_by['email'] = $userDetails->email;
            $created_by['profile_image'] = $userDetails->profile_image;
        }

        return [
            'id'                               => $this->uuid,
            'language'                         => $this->language,
            'type'                             => LabTypeResource::make($this->labType()),
            'mode'                             => LabModeResource::make($this->labMode()),
            'media_type'                       => $this->media_type,
            'media'                            => $media,
            'created_by'                       => $created_by,
            'source'                           => LabService::getSourceByLabId($this->id),
            'is_pre_build'                     => ($this->is_pre_built == '1' ? 'yes' : 'no'),
            'user'                             => UserService::joinName($this->user->first_name, $this->user->last_name),
            'organization_id'                  => $this->organization->uuid,
            'organization'                     => $this->organization->title,
            'hosted_by'                        => OrganizationHostResource::make($this->organization),
            'category_id'                      => $category_id,
            'category'                         => $category,
            'duration'                         => $duration,
            'duration_id'                      => $duration_id,
            'level'                            => $level,
            'level_id'                         => $level_id,
            'slug'                             => $this->slug,
            'title'                            => $this->title,
            'description'                      => $this->description,
            'privacy'                          => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                           => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'member_count'                     => $this->members()->count(),
            'total_share'                      => $this->shares()->count(),
            'is_auto_created'                  => ($this->is_auto_created == '1') ? 'yes' : 'no',
            'is_resource_sequential'           => ($this->is_resource_sequential == '1') ? 'yes' : 'no',
            'is_sequential'                    => ($this->is_sequential == '1') ? 'yes' : 'no',
            'is_achievement_enabled'           => ($this->is_achievement_enabled == '1') ? 'yes' : 'no',
            'is_notification_enabled'          => ($this->is_notification_enabled == '1') ? 'yes' : 'no',
            'is_verified'                      => ($this->is_verified == '1') ? 'yes' : 'no',
            'is_accessible'                    => ($this->is_accessible == '1') ? 'yes' : 'no',
            'is_live_event_enabled'            => $this->is_live_event_enabled ? 'yes' : 'no',
            'module_progress'                  => $module_progress,
            'live_event'                       => AirmeetEventResource::make($this->airmeet),
            'address'                          => $address,
            'achievement'                      => $achievement,
            'external_links'                   => LabExternalLinkResource::collection($this->external_links),
            'skills'                           => $skills,
            'skill_groups'                     => $skill_groups,
            'skill_stacks'                     => $skill_stacks,
            'likes'                            => $this->likes()->count(),
            'shares'                           => $this->shares()->count(),
            'lab_program_count'                => $this->lab_lab_program_association()->count(),
            'challenge_count'                  => $this->lab_challenge_association()->count(),
            'challenge_path_count'             => $this->lab_challenge_path_association()->count(),
            'resource_module_count'            => $this->lab_resource_module_association()->count(),
            'resource_collection_count'        => $this->lab_resource_collection_association()->count(),
            'resource_group_count'             => $this->lab_resource_group_association()->count(),
            'last_updated'                     => $this->updated_at,
            'campus_connect_opportunity'       => $campusConnectOpportunity,
            'campus_connect_story'             => $campusConnectStory,
            'campus_connect_status'            => data_get($this, 'campus_connect_status'),
        ];
    }
}
