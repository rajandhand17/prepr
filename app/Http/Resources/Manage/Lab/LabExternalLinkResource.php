<?php

namespace App\Http\Resources\Manage\Lab;

use Illuminate\Http\Resources\Json\JsonResource;

class LabExternalLinkResource extends JsonResource
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
            'title'     => optional($this->social_link_data)->title,
            'image'     => optional($this->social_link_data)->icon,
        ];
    }
}
