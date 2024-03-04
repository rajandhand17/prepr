<?php

namespace App\Http\Resources\Explore;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeaturedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->getLabs->uuid,
            'title'      => $this->getLabs->title,
            'description'=> $this->getLabs->description,
            'media'      => $this->getLabs->media,
        ];
    }
}
