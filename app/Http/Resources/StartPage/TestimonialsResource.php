<?php

namespace App\Http\Resources\StartPage;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstName=UserService::getUserById($this->user_id)->first_name;
        $lastName=UserService::getUserById($this->user_id)->last_name;
        $fullName=$firstName.' '.$lastName;
        return [
            'id'    => $this->id,
            'description' =>$this->description,
            'full_name' => $fullName,
        ];
    }
}
