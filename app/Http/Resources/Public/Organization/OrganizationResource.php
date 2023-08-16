<?php

namespace App\Http\Resources\Public\Organization;

use App\Helpers\UtilityHelper;
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
            $category = $this->getCategory->title;
        } else {
            $category = null;
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
            'category'                     => $category,
            'lab_count'                    => $this->members->count(),
            'challenge_count'              => 0,
            'resource_count'               => 0,
            'organization_users_count'     => 0,
            'likes'                        => $this->likes()->count(),
            'followers'                    => $this->followers()->count(),
            'shares'                       => $this->shares()->count(),
            'liked'                        => $this->liked(),
            'followed'                     => $this->followed(),
            'favourite'                    => $this->favourite(),
            'shared'                       => $this->shared(),
            'member_since'                 => UtilityHelper::formatDateTime($this->created_at),
            'organization_address'         => OrganizationAddressResource::collection($this->address),
            'organization_members'         => OrganizationMemberResource::collection($this->members),
        ];
    }
}
