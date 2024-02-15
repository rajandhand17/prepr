<?php

namespace App\Http\Resources\Public\Skill;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\WikipediaHelper;
class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skillDescription =WikipediaHelper::fetchSkillDescription($this->title, $request->language);
        $relatedSkills=WikipediaHelper::fetchRelatedSkills(config('app.jobs_recommendation_engine_url').$this->title);
         $data = [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $skillDescription ? $skillDescription : '',
            'related_skills'=> $relatedSkills ? $relatedSkills : '',
            'pinned'        => (isset($this->user_pinned->pinned)) ? $this->user_pinned->pinned : 'no',
        ];
        return $data;
    }
}
