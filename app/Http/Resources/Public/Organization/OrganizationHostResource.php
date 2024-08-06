<?php

namespace App\Http\Resources\Public\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationHostResource extends JsonResource
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
        return [
            'uuid'        => $this->uuid,
            'title'       => $this->title,
            'image'       => $this->cover_image,
            'description' => $this->description,
            'slug'        => $this->slug,
        ];
    }
}
