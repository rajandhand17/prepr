<?php

namespace App\Http\Resources\Manage\Challenge;

use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeExternalLinkResource extends JsonResource
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
            'id'                => $this->id,
            'title'             => $this->social_link['title'],
            'image'             => $this->social_link['icon'],
            'social_media_link' => $this->social_media_link,
            'social_link_id'    => $this->social_link_id,
        ];
    }
}
