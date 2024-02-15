<?php

namespace App\Http\Resources\Public\Skill;

use App\Helpers\WikipediaHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'related_skills'=> WikipediaHelper::fetchRelatedSkills(config('app.jobs_recommendation_engine_url').$this->title),
            'pinned'        => (isset($this->user_pinned->pinned)) ? $this->user_pinned->pinned : 'no',
        ];

        return $data;
    }
}
