<?php

namespace App\Http\Resources\Public\LabProgram;

use App\Http\Resources\Manage\Organization\OrganizationHostResource;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\UserService;
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
        $skills = [];
        $skill_groups = [];
        $skill_stacks = [];
        $category = null;
        $category_id = null;
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $organization = null;
        $organization_id = null;
        $module_progress = null;
        if ($this->getOrganization) {
            $organization = $this->getOrganization->title;
            $organization_id = $this->getOrganization->uuid;
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

        if ($this->achievement) {
            $achievement = [
                'achievement_name'      => $this->achievement->achievement_name,
                'achievement_points'    => $this->achievement->achievement_points,
                'achievement_image'     => $this->achievement->achievement_image,
            ];
        }

        if (auth('api')->check()) {
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
        }

        $join_status = 'No';
        $joined_status = $this->isJoined();
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

        $mode = null;
        if ($this->labProgramMode) {
            switch ($this->labProgramMode->value) {
                case '4':
                    $mode = 'team';
                    break;
                case '5':
                    $mode = 'individual';
                    break;
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
            'language'                      => $this->language,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'type'                          => LabProgramTypeResource::make($this->labProgramType()),
            'mode'                          => $mode,
            'is_joined'                     => $join_status,
            'created_by'                    => $created_by,
            'description'                   => $this->description,
            'hosted_by'                     => OrganizationHostResource::make($this->getOrganization),
            'user_id'                       => $this->user_id,
            'media_type'                    => $this->media_type,
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
            'achievement'                   => $achievement,
            'favourite'                     => $this->favourite(),
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'is_achievement_enabled'        => ($this->is_achievement_enabled == '1') ? 'yes' : 'no',
            'is_sequential'                 => ($this->is_sequential == '1') ? 'yes' : 'no',
            'is_accessible'                 => ($this->is_accessible == '1') ? 'yes' : 'no',
            'liked'                         => $this->liked(),
            'likes'                         => $this->likes()->count(),
            'shares'                        => $this->shares()->count(),
            'module_progress'               => $module_progress,
            'member_count'                  => '0', //Static for temporary basis
            'last_updated'                  => $this->updated_at,
        ];
    }
}
