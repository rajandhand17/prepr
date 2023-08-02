<?php

namespace App\Http\Resources\Public\Lab;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Public\Organization\OrganizationAddressResource;
use App\Http\Resources\Public\Organization\OrganizationMemberResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
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
            'id'                           => $this->uuid,
            'language'                     => $this->language,
            'title'                        => $this->title,
            'slug'                         => $this->slug,
            'description'                  => $this->description,
            'privacy'                      => $this->type,
            'media_type'                   => $this->media_type,
            'media'                        => $this->media,
            'status'                       => $this->status,
        ];
    }
}
