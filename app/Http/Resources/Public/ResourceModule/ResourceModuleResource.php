<?php

namespace App\Http\Resources\Public\ResourceModule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $links=[];
        $files=[];
        $documents=[];
        $video=[];
        $audio=[];
        if($this->url){
            $links=$this->url;
        }
        if($this->image){
            $files=$this->image;
        }
        if($this->documents){
            $documents=$this->documents;
        }
        if($this->video){
            $video=$this->video;
        }
        if($this->audio){
            $audio=$this->audio;
        }
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
            'links'                                   => $links,
            'files'                                   => $files,
            'documents'                               => $documents,
            'video'                                   => $video,
            'audio'                                   => $audio,

        ];
    }
}
