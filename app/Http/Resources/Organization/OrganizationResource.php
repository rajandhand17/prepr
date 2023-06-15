<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'id' => $this->id,
            'language' => $this->language,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'profile_image' => $this->profile_image,
            'website' => $this->website,
            'about' => $this->about,
            'status' => $this->status,
            'total_employees' => $this->total_employees,
            'latitude'=>$this->organizationAddress[0]->latitude,
            'longitude'=>$this->organizationAddress[0]->longitude,
            'address'=>$this->organizationAddress[0]->address,
            'city'=>$this->organizationAddress[0]->city,
            'state'=>$this->organizationAddress[0]->state,
            'country'=>$this->organizationAddress[0]->country,
            'members'=>$this->organizationMembers,
            'lab'=>0,
            'challanges'=>0,
            'resources'=>0,
        ];
    }
}
