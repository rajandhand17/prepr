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
        $privacy = '';
        $status = '';
        $is_global = '';
        if ($this->urls) {
            $links = $this->urls->map(function ($index) {
                return [
                    'id'            => $index->id,
                    'title'         => $index->title,
                    'path'          => $index->getRawOriginal('path'),
                    'social_link_id'=> $index->social_link_id,
                ];
            })->all();
        }
        if ($this->images) {
            $files = $this->images;
        }
        if ($this->documents) {
            $document = $this->documents;
        }
        if ($this->videos) {
            $video = $this->videos;
        }
        if ($this->audios) {
            $audio = $this->audios;
        }
        if ($this->embedded_videos) {
            $embedded_video = $this->embedded_videos->map(function ($index) {
                return [
                    'id'    => $index->id,
                    'title' => $index->title,
                    'path'  => $index->getRawOriginal('path'),
                ];
            })->all();
        }
        if ($this->embedded_audios) {
            $embedded_audio = $this->embedded_audios->map(function ($index) {
                return [
                    'id'    => $index->id,
                    'title' => $index->title,
                    'path'  => $index->getRawOriginal('path'),
                ];
            })->all();
        }
        switch($this->privacy) {
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
            'likes'                                   => $this->likes()->count(),
            'shares'                                  => $this->shares()->count(),
            'liked'                                   => $this->liked(),
            'favourite'                               => $this->favorites(),
        ];
    }
}
