<?php

namespace App\Http\Resources\Career;

use App\Http\Resources\Master\SkillResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'title'            => $this->title,
            'skills'           => SkillResource::collection($this->skills),
            'lightcast_id'     => $this->lightcast_id,
            'related_challenge'=> $this->related_challenge->count(),
            'related_labs'     => $this->related_labs->count(),
            'related_resource' => $this->related_resource->count(),
            'saved_on'         => $this->created_on,
            'pinned'           => $this->pinned,
        ];
    }
}
