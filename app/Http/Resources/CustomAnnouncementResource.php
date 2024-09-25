<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomAnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'custom_announcement_title'          => $this->custom_announcement_title,
            'custom_announcement_type'           => $this->custom_announcement_type == '0' ? 'email' : 'notification',
            'custom_announcement_number'         => $this->custom_announcement_number,
            'custom_announcement_duration'       => $this->custom_announcement_duration,
            'custom_announcement_description'    => $this->custom_announcement_description,
        ];
    }
}
