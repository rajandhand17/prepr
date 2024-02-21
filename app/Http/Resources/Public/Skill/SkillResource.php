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
        $relatedSkills = WikipediaHelper::fetchRelatedSkills(config('app.skills_recommendation_engine_url').strtolower($this->title));
        $key = gettype($relatedSkills) == 'array' ? array_keys($relatedSkills) : [];
        $relatedKeyUrl = isset($key[0]) ? config('app.wikipedia_url').str_replace(' ', '_', $key[0]) : [];

        $data = [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => $skillDescription !== false ? $skillDescription : '',
            'related_skills'         => $key !== false ? $key : [],
            'related_skill_show_more'=> $relatedKeyUrl,
            'is_saved'               => (!empty(UserSkillsService::checkUserSkillExists($this->id))) ? 'yes' : 'no',
            'related_jobs'           => [],
        ];
        if (isset($this->user_pinned->pinned)) {
            $data['pinned'] = $this->user_pinned->pinned == 1 ? 'yes' : 'no';
        }

        return $data;
    }
}
