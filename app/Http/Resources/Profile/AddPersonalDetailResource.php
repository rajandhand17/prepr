<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddPersonalDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'title'  => $this->title,
            'name'    => $this->name,
            'patent_date'=> $this->patent_date,
            'description'=> $this->description,
        ];

        return $response;
    }
}
