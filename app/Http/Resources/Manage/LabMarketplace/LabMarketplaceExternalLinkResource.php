<?php

namespace App\Http\Resources\Manage\LabMarketplace;

use Illuminate\Http\Resources\Json\JsonResource;

class LabMarketplaceExternalLinkResource extends JsonResource
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
            'social_media_link' => $this->social_media_link,
            'social_link_id'    => $this->social_link_id,
        ];
    }
}
