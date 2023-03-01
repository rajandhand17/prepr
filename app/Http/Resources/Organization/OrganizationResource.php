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
            'display_name' => $this->display_name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'profile_image' => $this->profile_image,
            'website' => $this->website,
            'about' => $this->about,
            'category' => $this->category,
            'status' => $this->status,
            'total_employees' => $this->total_employees,
        ];
    }
}
