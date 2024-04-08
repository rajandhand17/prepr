<?php

namespace App\Http\Resources\Manage\ChallengePath;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengePathListNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->uuid,
            'title'=> $this->title,
            'media'=> $this->media,
        ];
    }
}
