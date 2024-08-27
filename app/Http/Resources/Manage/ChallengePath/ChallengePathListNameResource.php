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
        if ($this->media == config('site-settings.aws_url').config('site-settings.default_challenge_path_cover_image') || $this->media == config('site-settings.aws_url')) {
            $this->media = null;
        }

        return [
            'id'   => $this->uuid,
            'title'=> $this->title,
            'media'=> $this->media,
        ];
    }
}
