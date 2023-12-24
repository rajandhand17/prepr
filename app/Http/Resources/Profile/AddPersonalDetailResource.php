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
            'name'   => auth()->user()->first_name.' '.auth()->user()->last_name,
            'email'  => auth()->user()->email,
            'age'    => $this->age,
            'about'  => $this->about,
            'gender' => $this->gender,
        ];

        return $response;
    }
}
