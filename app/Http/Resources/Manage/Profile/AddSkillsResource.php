<?php

namespace App\Http\Resources\Manage\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddSkillsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'id'          => $this->id,
            'skill_id'    => $this->skill,
        ];

        return $response;
    }
}
