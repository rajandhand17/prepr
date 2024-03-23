<?php

namespace App\Http\Resources\Public\Skill;

use App\Helpers\WikipediaHelper;
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
        $skillDescription = WikipediaHelper::fetchSkillDescription($this->title, $request->language);
        $relatedSkills = WikipediaHelper::fetchRelatedSkills(config('wikipedia.SKILLS_RECOMMENDATION_ENGINE_URL').strtolower($this->title));
        $key = gettype($relatedSkills) == 'array' ? array_keys($relatedSkills) : [];
        $relatedKeyUrl = isset($key[0]) ? config('wikipedia.WIKIPEDIA_URL').str_replace(' ', '_', $key[0]) : [];
        $data = [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => $skillDescription !== false ? $skillDescription : '',
            'related_skills'         => $key !== false ? $key : [],
            'related_skill_url'      => $relatedKeyUrl,
            'is_saved'               => (!empty(UserSkillsService::checkUserSkillExists($this->id))) ? 'yes' : 'no'
        ];
        if (isset($this->user_pinned->pinned)) {
            $data['pinned'] = $this->user_pinned->pinned == 1 ? 'yes' : 'no';
        }

        return $data;
    }
}
