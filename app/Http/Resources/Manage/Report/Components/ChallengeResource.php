<?php

namespace App\Http\Resources\Manage\Report\Components;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'          => $this->uuid,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'members_count' => $this->whenCounted('members'),
            'updated_at'    => $this->updated_at,
        ];
    }
}
