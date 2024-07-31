<?php

namespace App\Http\Resources\Public\Organization;

use App\Helpers\UtilityHelper;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
        $category = $this->getCategory;

        if ($category) {
            $category_id = $this->getCategory->id;
            $category = $this->getCategory->title;
        } else {
            $category = null;
            $category_id = null;
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
            'id'                           => $this->uuid,
            'language'                     => $this->language,
            'title'                        => $this->title,
            'slug'                         => $this->slug,
            'description'                  => $this->description,
            'cover_image'                  => $this->cover_image,
            'profile_image'                => $this->profile_image,
            'custom_url'                   => $this->custom_url,
            'website'                      => $this->website,
            'about'                        => $this->about,
            'total_employees'              => $this->total_employees,
            'category_id'                  => $category_id,
            'category'                     => $category,
            'lab_count'                    => $this->labs_count()->count(),
            'challenge_count'              => $this->challenges_count()->count(),
            'resource_count'               => $this->resource_modules_count()->count(),
            'organization_users_count'     => $this->members->count(),
            'likes'                        => $this->likes()->count(),
            'followers'                    => $this->followers()->count(),
            'shares'                       => $this->shares()->count(),
            'liked'                        => $this->liked(),
            'followed'                     => $this->followed(),
            'favourite'                    => $this->favourite(),
            'is_verified'                  => ($this->is_verified == '1' ? 'yes' : 'no'),
            'is_onboarding_completed'      => ($this->is_onboarding_completed == '0') ? 'no' : 'yes',
            'member_since'                 => UtilityHelper::formatDateTime($this->created_at),
            'organization_type'            => OrganizationTypeModeResource::collection($this->organizationType),
            'custom_login_register'        => OrganizationCustomizationResource::make($this->customization_login_register),
            'organization_address'         => OrganizationAddressResource::collection($this->address),
            'organization_members'         => OrganizationMemberResource::collection($this->organizationMembers),
            'organization_details'         => OrganizationChargebeeLimitResource::make($this),
            'external_links'               => OrganizationExternalLinkResource::collection($this->external_links),
            'created_by'                   => $created_by,
        ];
    }
}
