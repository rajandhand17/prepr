<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'company'       => $this->company,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'address'       => $this->address,
            'state'         => $this->state,
            'country'       => $this->country,
        ];

        return $response;
    }
}
