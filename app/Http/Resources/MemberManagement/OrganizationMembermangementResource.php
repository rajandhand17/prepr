<?php

namespace App\Http\Resources\MemberManagement;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationMembermangementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                          => $this->id,
            'language'                    => $this->language,
            'name'                        => $this->name,
            'slug'                        => $this->slug,
            'description'                 => $this->description,
            'cover_image'                 => $this->cover_image,
            'profile_image'               => $this->profile_image,
            'website'                     => $this->website,
            'about'                       => $this->about,
            'status'                      => $this->status,
            'total_employees'             => $this->total_employees,
            'lab_count'                   => 0,
            'challange_count'             => 0,
            'resource_count'              => 0,
            'organization_users_count'    => 0,
        ];
    }
}
