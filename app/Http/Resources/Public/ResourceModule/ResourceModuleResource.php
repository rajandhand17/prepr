<?php

namespace App\Http\Resources\Public\ResourceModule;

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
        $links = [];
        $files = [];
        $document = [];
        $video = [];
        $audio = [];
        $privacy="";
        $status="";
        $is_global="";
        if ($this->url) {
            $links = $this->url;
        }
        if ($this->image) {
            $files = $this->image;
        }
        if ($this->document) {
            $document = $this->document;
        }
        if ($this->video) {
            $video = $this->video;
        }
        if ($this->audio) {
            $audio = $this->audio;
        }
        if($this->embedded_video){
            $embedded_video = $this->embedded_video;
        }
        if($this->embedded_audio){
            $embedded_audio = $this->embedded_audio;
        }
        switch($this->privacy){
            case '0':
                $privacy = 'yes';
                break;
            case '1':
                $privacy = 'no';
                break;
            default:
                $privacy = 'no';
                break;
        }

        switch($this->status) {
            case '0':
                $status = 'draft';
                break;
            case '1':
                $status = 'published';
                break;
            case '2':
                $status = 'archive';
                break;
            default:
                $status = 'draft';
                break;
        }
        switch($this->is_global) {
            case '0':
                $is_global = 'yes';
                break;
            case '1':
                $is_global = 'no';
                break;
            default:
                $is_global = 'no';
                break;
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
            'privacy'                                 => $privacy,
            'status'                                  => $status,
            'is_global'                               => $is_global,
            'links'                                   => $links,
            'files'                                   => $files,
            'documents'                               => $document,
            'video'                                   => $video,
            'audio'                                   => $audio,
            'embedded_video'                          => $embedded_video,
            'embedded_audio'                          => $embedded_audio,
        ];
    }
}
