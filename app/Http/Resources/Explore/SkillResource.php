<?php

namespace App\Http\Resources\Explore;

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
        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'relatedSkills'          => WikipediaHelper::fetchRelatedSkills(config('wikipedia.SKILLS_RECOMMENDATION_ENGINE_URL').strtolower($this->title)),
            'challenges'             => $this->getChallenges!=null ? $this->getChallenges->count() : '0',
            'labs'                   => $this->getLabs!=null ? $this->getLabs->count() : '0',
            'related_resources'      =>$this->getLlatedResources!=null ? $this->getLlatedResources->count() : '0',
        ];
    }
}
