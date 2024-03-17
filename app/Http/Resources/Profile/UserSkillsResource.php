<?php

namespace App\Http\Resources\Profile;

use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSkillsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skills = SkillService::getSkillBasedOnId($this->skill);

        return [
            'id'   => $this->id,
            'skill'=> $skills,
        ];
    }
}
