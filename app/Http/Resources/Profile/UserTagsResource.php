<?php

namespace App\Http\Resources\Profile;

use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTagsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tags = TagService::getTagsIdBasedOnId($this->tag_id)->pluck('title', 'id');

        return [
            'id'   => $this->id,
            'tag'  => $tags,
        ];
    }
}
