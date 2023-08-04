<?php

namespace App\Http\Resources\Public\Lab;

use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
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
        $category = $this->getCategory;
        if ($category) {
            $category = $this->getCategory->title;
        } else {
            $category = null;
        }

        return [
            'id'                           => $this->uuid,
            'language'                     => $this->language,
            'title'                        => $this->title,
            'slug'                         => $this->slug,
            'description'                  => $this->description,
            'privacy'                      => $this->type,
            'media_type'                   => $this->media_type,
            'media'                        => $this->media,
            'category'                     => $category,
            'status'                       => $this->status,
            'likes'                        => $this->join()->count(),
            'followers'                    => $this->followers()->count(),
            'shares'                       => $this->shares()->count(),
            'lab_address'                  => LabAddressResource::collection($this->address),
            'lab_achievement'              => LabAchievementResource::collection($this->achievement),
            'lab_external_links'           => LabExternalLinksResource::collection($this->external_links),
        ];
    }
}
