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
        $category = $this->category;
        if ($category) {
            $category = $this->category->title;
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
        ];
    }
}
