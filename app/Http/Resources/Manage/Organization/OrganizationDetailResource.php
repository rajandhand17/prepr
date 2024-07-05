<?php

namespace App\Http\Resources\Manage\Organization;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Manage\Lab\LabResource;
use App\Http\Resources\Manage\MemberManagement\MemberManagementResource;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = ($this->status == '0') ? 'Draft' : (($this->status == '1') ? 'Published' : (($this->status == '2') ? 'Deactivated' : 'Archived'));
        $category = $this->getCategory;
        if ($category) {
            $category_id = $this->getCategory->id;
            $category = $this->getCategory->title;
        } else {
            $category = null;
            $category_id = null;
        }

        if (empty($this->chargebee_details)) {
            $feedChargeBeeDetails = ChargebeeHelper::createChargebeePlanDetails($this->id);
        }

        $custom_url = null;
        if ($this->getRawOriginal('custom_url')) {
            $custom_url = $this->getRawOriginal('custom_url');
        }

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'description'                   => $this->description,
            'cover_image'                   => $this->cover_image,
            'profile_image'                 => $this->profile_image,
            'website'                       => $this->website,
            'custom_url'                    => $custom_url,
            'about'                         => $this->about,
            'status'                        => $status,
            'total_employees'               => $this->total_employees,
            'category_id'                   => $category_id,
            'category'                      => $category,
            'is_verified'                   => ($this->is_verified == '1' ? 'yes' : 'no'),
            'is_onboarding_completed'       => ($this->is_onboarding_completed == 0) ? 'no' : 'yes',
            'lab_count'                     => $this->labs_count()->count(),
            'challenge_count'               => $this->challenges_count()->count(),
            'resource_count'                => $this->resource_modules_count()->count(),
            'organization_users_count'      => $this->members->count(),
            'member_since'                  => UtilityHelper::formatDateTime($this->created_at),
            'organization_type'             => OrganizationTypeModeResource::collection($this->organizationType),
            'organization_address'          => OrganizationAddressResource::collection($this->address),
            'organization_members'          => OrganizationMemberResource::collection($this->organizationMembers),
            'organization_people'           => MemberManagementResource::collection($this->members),
            'external_links'                => OrganizationExternalLinkResource::collection($this->external_links),
            'organization_details'          => OrganizationChargebeeLimitResource::make($this),
            'custom_login_register'         => OrganizationCustomizationResource::make($this->customization_login_register),
            'labs'                          => LabResource::collection($this->labs->take(config('site-settings.skills_par_module_limit'))),
            'challenges'                    => ChallengeResource::collection($this->challenges_count->take(config('site-settings.skills_par_module_limit'))),
            'resource_modules'              => ResourceModuleResource::collection($this->resource_modules_count->take(config('site-settings.skills_par_module_limit'))),
        ];
    }
}
