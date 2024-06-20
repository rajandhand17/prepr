<?php

namespace App\Http\Resources\Public\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationExternalLinkResource extends JsonResource
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
            'id'        => $this->id,
            'link_id'   => $this->social_link_id,
            'link'      => $this->social_media_link,
            'title'     => $this->social_link['title'],
            'image'     => $this->social_link['icon'],
        ];
    }
}
