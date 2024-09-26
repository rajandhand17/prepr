<?php

namespace App\Http\Resources\Profile;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'university'    => $this->university,
            'description'   => $this->description,
            'degree'        => $this->degree,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'address'       => $this->address,

        ];
    }
}
