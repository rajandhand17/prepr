<?php

namespace App\Http\Resources\Manage\ChannelApi;

use App\Helpers\UtilityHelper;
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
        $frontendUrl = UtilityHelper::sanitizeUrl(config('site-settings.frontend_site_url'));

        return [
            'id'          => $this->id,
            'name'        => $this->title,
            'description' => $this->description,
            'provided_by' => data_get($this->organization, 'display_name') ?? data_get($this->organization, 'title', '-'),
            'image'       => $this->media,
            'url'         => sprintf('%s/challenge/%s', $frontendUrl, $this->slug),
            'p_type'      => 'challenges',
        ];
    }
}
