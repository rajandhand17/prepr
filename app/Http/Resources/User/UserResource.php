<?php

namespace App\Http\Resources\User;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Manage\LabProgram\LabProgramListNameResource;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionListNameResource;
use App\Http\Resources\Manage\ResourceGroup\ResourceGroupListNameResource;
use App\Http\Resources\Profile\UserExperienceResource;
use App\Http\Resources\Profile\UserSkillsResource;
use App\Http\Resources\Settings\UserNotificationResource;
use App\Http\Resources\Settings\UserPrivacyResource;
use App\Http\Resources\UserPersonalResource;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        $roles = $this->roles->pluck('display_name');
        if ($roles) {
            $roles = array_unique($roles->toArray());
        } else {
            $roles = [];
        }

        $organization_details = null;
        $fetchOrganization = OrganizationService::getOrganizationExistBasedOnId($this->preferred_organization);
        if (!$fetchOrganization) {
            $fetchOrganization = OrganizationService::fetchOrganizationBasedOnUserId($this->id);
        }
        if ($fetchOrganization) {
            $upgrade_plan_enable = ($this->id == $fetchOrganization->user_id) ? 'yes' : 'no';
            $is_onboarding_completed = ($upgrade_plan_enable == 'yes') ? (($fetchOrganization->is_onboarding_completed == '0') ? 'no' : 'yes') : 'N/A';
            $organization_details['id'] = $fetchOrganization->uuid;
            $organization_details['title'] = $fetchOrganization->title;
            $organization_details['slug'] = $fetchOrganization->slug;
            $organization_details['upgrade_plan_enable'] = $upgrade_plan_enable;
            $organization_details['is_onboarding_completed'] = $is_onboarding_completed;
            $organization_details['count_of_resource_module']=count($fetchOrganization->resource_modules_count);
            $organization_details['lab_program']=LabProgramListNameResource::collection($fetchOrganization->lab_programs_count->take(config('site-settings.switcher_listing_limit')));
            $organization_details['resource_group']=ResourceGroupListNameResource::collection($fetchOrganization->resource_groups_count->take(config('site-settings.switcher_listing_limit')));
            $organization_details['resource_collection']=ResourceCollectionListNameResource::collection($fetchOrganization->resource_collections_count->take(config('site-settings.switcher_listing_limit')));
        }

        $memberManagement = new MemberManagementService();

        return [
            'id'                          => $this->id,
            'preferred_language'          => $this->preferred_language,
            'preferred_timezone'          => $this->preferred_timezone ? $this->preferred_timezone : 'EST',
            'first_name'                  => $this->first_name,
            'last_name'                   => $this->last_name,
            'full_name'                   => $this->full_name,
            'username'                    => $this->username,
            'email'                       => $this->email,
            'phone_number'                => $this->phone_number,
            'profile_image'               => $this->profile_image,
            'two_factor_verification'     => ($this->two_factor_verification == '0') ? 'no' : 'yes',
            'is_onboarding_completed'     => ($this->is_onboarding_completed == '0') ? 'no' : 'yes',
            'user_points'                 => $this->user_points,
            'user_rank'                   => $this->verified_user,
            'verified_user'               => $this->verified_user,
            'referral_code'               => $this->referal_code,
            'is_profile_completed'        => ($this->is_profile_completed == '0') ? 'no' : 'yes',
            'member_since'                => UtilityHelper::formatDateTime($this->created_at),
            'roles'                       => $roles,
            'is_lab_onboarding'           => ($this->display_lab_mini_onboarding == 0) ? 'no' : 'yes',
            'is_challenge_onboarding'     => ($this->display_challenge_mini_onboarding == 0) ? 'no' : 'yes',
            'is_organization_onboarding'  => ($this->display_organization_mini_onboarding == 0) ? 'no' : 'yes',

            'user_experiences' => UserExperienceResource::collection($this->userExperience),
            'go1'              => [
                'can_create_resource' => $memberManagement->canCreateGO1Resource($this),
                'can_play_resource'   => $memberManagement->canPlayGO1Resoruces($this),
            ],
            'notification'     => UserNotificationResource::make($this->userSetting),
            'privacy'          => UserPrivacyResource::make($this->userSetting),
            'sso_integrations' => [
                'linked-in' => 'inactive',
                'google'    => 'inactive',
                'magnet'    => 'inactive',
                'microsoft' => 'inactive',
                'apple'     => 'inactive',
            ],
            'organization_details' => $organization_details,

            'resume'                  => $this->userResume ? 'yes' : 'no',
            'user_projects_count'     => $this->whenCounted('userProjects'),
            'user_skills_count'       => $this->whenCounted('userSkills'),
            'user_labs_count'         => $this->whenCounted('userLabs'),
            'user_achievements_count' => $this->whenCounted('userAchievements'),
            'user_personal'           => new UserPersonalResource($this->whenLoaded('userPersonal')),
            'skills'                  => UserSkillsResource::collection($this->whenLoaded('userSkills')),
        ];
    }
}
