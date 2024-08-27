<?php

namespace App\Http\Resources\Public\ResourceCollection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCollectionListNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->media==config('site-settings.aws_url') || $this->media==config('site-settings.aws_url').config('site-settings.default_resource_collection_cover_image')){
            $this->media=null;
        }
        return [
            'uuid'  => $this->uuid,
            'title' => $this->title,
            'media' => $this->media,
        ];
    }
}
