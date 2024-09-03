<?php

namespace App\Http\Resources\Public\Skill;

use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddSkillResource extends JsonResource
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
            'id'     => $this->id,
            'skill'  => $skills,
            'pinned' => $this->pinned == '1' ? 'yes' : 'no',
        ];
    }
}
