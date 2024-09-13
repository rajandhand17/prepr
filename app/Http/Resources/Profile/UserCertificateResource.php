<?php

namespace App\Http\Resources\Profile;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCertificateResource extends JsonResource
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
            'name'          => $this->name,
            'company'       => $this->company,
            'description'   => $this->description,
            'start_date'    => UtilityHelper::formatDateTime($this->start_date),
            'end_date'      => UtilityHelper::formatDateTime($this->end_date),

        ];
    }
}
