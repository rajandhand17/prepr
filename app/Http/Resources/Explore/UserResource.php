<?php

namespace App\Http\Resources\Explore;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_resume'=>empty($this->userResumeFiles)? 'no' :'yes',
            'is_skills'=>empty($this->userSkills)? 'no' :'yes',
        ];
    }
}
