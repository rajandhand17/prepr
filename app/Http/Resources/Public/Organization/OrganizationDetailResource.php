<?php

namespace App\Http\Resources\Public\Organization;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Http\Resources\Public\Lab\LabResource;
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
        $category = $this->getCategory;

        if ($category) {
            $category_id = $this->getCategory->id;
            $category = $this->getCategory->title;
        } else {
            $category = null;
            $category_id = null;
        }

        return [
            'id'                           => $this->uuid,
            'language'                     => $this->language,
            'title'                        => $this->title,
            'slug'                         => $this->slug,
            'description'                  => $this->description,
            'cover_image'                  => $this->cover_image,
            'profile_image'                => $this->profile_image,
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
            'labs'                         => LabResource::collection($this->labs),
            'challenges'                   => ChallengeResource::collection($this->challenges_count),
            'resource_modules'             => ResourceModuleResource::collection($this->resource_modules_count),
            'member_since'                 => UtilityHelper::formatDateTime($this->created_at),
            'organization_address'         => OrganizationAddressResource::collection($this->address),
            'organization_members'         => OrganizationMemberResource::collection($this->organizationMembers),
            'organization_details'         => OrganizationChargebeeLimitResource::make($this),
        ];
    }
}
