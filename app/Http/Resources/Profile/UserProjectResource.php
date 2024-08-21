<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Format the response data
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'slug'         => $this->slug,
            'media'        => $this->media,
            'media_type'   => $this->media_type,
            'challenge'    => [
                'slug'  => $this->challenge->slug,
                'title' => $this->challenge->title,
            ],
        ];
    }
}
