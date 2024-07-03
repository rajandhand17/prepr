<?php

namespace App\Http\Resources\Explore;

use App\Helpers\WikipediaHelper;
use App\Services\SkillService;
use App\Services\UserSkillsService;
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
        $related_skills = WikipediaHelper::fetchRelatedSkills(config('wikipedia.SKILLS_RECOMMENDATION_ENGINE_URL').strtolower($this->title));
        $count_related_skills = (is_array($related_skills)) ? count($related_skills) : '0';
        $related_skilled = is_array($related_skills) ? array_keys($related_skills) : [];
        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'related_skills'         => $related_skilled,
            'related_skills_count'   => $count_related_skills,
            'related_challenges'     => $this->getChallenges != null ? $this->getChallenges->count() : '0',
            'related_labs'           => $this->getLabs != null ? $this->getLabs->count() : '0',
            'related_resources'      => $this->getLlatedResources != null ? $this->getLlatedResources->count() : '0',
        ];
    }
}
