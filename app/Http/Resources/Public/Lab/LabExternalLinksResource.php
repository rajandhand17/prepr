<?php

namespace App\Http\Resources\public\Lab;

use App\Models\SocialLink;
use Illuminate\Http\Resources\Json\JsonResource;

class LabExternalLinksResource extends JsonResource
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
            'id'              => $this->id,
            'social_media_link'=> $this->social_media_link,
        ];
    }
}
