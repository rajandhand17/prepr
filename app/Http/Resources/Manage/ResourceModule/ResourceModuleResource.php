<?php

namespace App\Http\Resources\Manage\ResourceModule;

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
            'user'                                    => $this->users->first_name.' '.$this->users->last_name,
            'organization_id'                         => $this->organization_id,
            'slug'                                    => $this->slug,
            'description'                             => $this->description,
            'media_type'                              => $this->media_type,
            'cover_image'                             => $this->media,
            'privacy'                                 => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                                  => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'is_global'                               => ($this->is_global == '1') ? 'yes' : 'no',
            'links'                                   => $this->resource_module_url,
            'files'                                   => $this->resource_module_image,
            'documents'                               => $this->resource_module_document,
            'video'                                   => $this->resource_module_video,
            'audio'                                   => $this->resource_module_audio,

        ];
    }
}
