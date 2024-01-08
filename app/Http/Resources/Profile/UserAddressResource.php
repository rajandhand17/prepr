<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'latitude'        => $this->id,
            'longitude'       => $this->longitude,
            'address'         => $this->address,
            'city'            => $this->city,
            'state'           => $this->state,
            'country'         => $this->country,
            'zip_code'        => $this->zip_code,
        ];
    }
}
