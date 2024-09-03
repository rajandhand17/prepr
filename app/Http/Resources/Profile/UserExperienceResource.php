<?php

namespace App\Http\Resources\Profile;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExperienceResource extends JsonResource
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
            'company'       => $this->company,
            'position'      => $this->position,
            'start_date'    => UtilityHelper::formatDateTime($this->start_date),
            'end_date'      => UtilityHelper::formatDateTime($this->end_date),
            'address'       => $this->address,
            'state'         => $this->state,
            'country'       => $this->country,
            'description'   => $this->description,
        ];
    }
}
