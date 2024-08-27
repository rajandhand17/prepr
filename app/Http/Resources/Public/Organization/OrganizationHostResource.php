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
        if ($this->cover_image == config('site-settings.aws_url').config('site-settings.default_organization_cover_image')) {
            $this->cover_image = null;
        }

        return [
            'uuid'        => $this->uuid,
            'title'       => $this->title,
            'image'       => $this->cover_image,
            'description' => $this->description,
            'slug'        => $this->slug,
        ];
    }
}
