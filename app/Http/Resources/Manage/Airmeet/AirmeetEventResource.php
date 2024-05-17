<?php

namespace App\Http\Resources\Manage\Airmeet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AirmeetEvent
 */
class AirmeetEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->airmeet_event_id,
            'url'      => $this->airmeet_event_url,
        ];
    }
}
