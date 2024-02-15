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
        $skillDescription = WikipediaHelper::fetchSkillDescription($this->title, $request->language);
        $relatedSkills = WikipediaHelper::fetchRelatedSkills(config('app.skills_recommendation_engine_url').strtolower($this->title));
        $data = [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $skillDescription !== false ? $skillDescription : '',
            'related_skills'=> $relatedSkills !== false ? $relatedSkills : '',
            'pinned'        => (isset($this->user_pinned->pinned)) ? $this->user_pinned->pinned : 'no',
        ];

        return $data;
    }
}
