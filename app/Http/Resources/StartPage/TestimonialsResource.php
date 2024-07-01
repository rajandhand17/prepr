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
        $profileImage = $userData->profile_image;
        $fullName = $userData->full_name;

        return [
            'id'            => $this->id,
            'description'   => $this->description,
            'full_name'     => $fullName,
            'profile_image' => $profileImage,
        ];
    }
}
