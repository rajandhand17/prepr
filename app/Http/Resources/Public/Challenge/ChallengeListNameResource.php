<?php

namespace App\Http\Resources\Public\Challenge;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeListNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
                break;
            default:
                $media = $this->media;
                break;
        }
        if($media==config('site-settings.aws_url').config('site-settings.default_challenge_cover_image') || $media==config('site-settings.aws_url')){
            $media=null;
        }
        return [
            'uuid'    => $this->uuid,
            'title'   => $this->title,
            'slug'    => $this->slug,
            'media'   => $media,
        ];
    }
}
