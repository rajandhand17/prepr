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
        $userData = UserService::getUserById($this->user_id);
        $firstName = $userData->first_name;
        $lastName = $userData->last_name;
        $profileImage = $userData->profile_image;
        $fullName = $firstName.' '.$lastName;

        return [
            'id'            => $this->id,
            'description'   => $this->description,
            'full_name'     => $fullName,
            'profile_image' => $profileImage,
        ];
    }
}
