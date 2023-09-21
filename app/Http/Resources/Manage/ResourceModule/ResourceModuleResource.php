<?php

namespace App\Http\Resources\Manage\ResourceModule;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Manage\Organization\OrganizationAddressResource;
use App\Http\Resources\Manage\Organization\OrganizationMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {

        return [
            'id'                                      => $this->uuid,
            'language'                                => $this->language,
            'title'                                   => $this->title,
            'user_id'                                 => $this->user_id,
            'organization_id'                         => $this->organization_id,
            'slug'                                    => $this->slug,
            'description'                             => $this->description,
            'media_type'                              => $this->media_type,
            'media'                                   => $this->media,
            'privacy'                                 => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                                  => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'is_global'                               => ($this->is_global == '1') ? 'yes' : 'no',

        ];
    }
}
