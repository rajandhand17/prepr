<?php

namespace App\Http\Resources\Public\Lab;

use App\Http\Resources\Public\Airmeet\AirmeetEventResource;
use App\Http\Resources\Public\Organization\OrganizationHostResource;
use App\Services\AchievementConditionListService;
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
        $category_id = null;
        $category = null;
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $achievement = [];
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
        if ($media == config('site-settings.aws_url').config('site-settings.default_lab_cover_image') || $media == config('site-settings.aws_url')) {
            $media = null;
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
            'id'                            => $this->uuid,
            'type'                          => LabTypeResource::make($this->labType()),
            'mode'                          => LabModeResource::make($this->labMode()),
            'created_by'                    => $created_by,
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
            'organization_slug'             => isset($this->organization->slug) ? $this->organization->slug : null,
            'duration'                      => $duration,
            'duration_id'                   => $duration_id,
            'level'                         => $level,
            'level_id'                      => $level_id,
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'member_count'                  => $this->members()->count(),
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
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
            'lab_program_count'             => $this->lab_lab_program_association()->count(),
            'challenge_count'               => $this->lab_challenge_association()->count(),
            'challenge_path_count'          => $this->lab_challenge_path_association()->count(),
            'resource_module_count'         => $this->lab_resource_module_association()->count(),
            'resource_collection_count'     => $this->lab_resource_collection_association()->count(),
            'resource_group_count'          => $this->lab_resource_group_association()->count(),
            'last_updated'                  => $this->updated_at,
        ];
    }
}
