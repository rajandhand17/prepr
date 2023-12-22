<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddEducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $response = [
            'university'  => $this->university,
            'degree'      => $this->degree,
            'purpose'     => $this->purpose,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
            'address'     => $this->address,
        ];

        return $response;
    }
}
