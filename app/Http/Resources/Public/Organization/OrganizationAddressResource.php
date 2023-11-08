<?php

namespace App\Http\Resources\Public\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'full_address'    => $this->full_address,
            'address_1'       => $this->address_1,
            'address_2'       => $this->address_2,
            'city'            => $this->city,
            'state'           => $this->state,
            'country'         => $this->country,
            'zip_code'        => $this->zip_code,
        ];
    }
}
