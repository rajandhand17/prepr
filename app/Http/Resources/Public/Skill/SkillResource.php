<?php

namespace App\Http\Resources\Public\Skill;

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
        $skillDescription = WikipediaHelper::fetchSkillDescription($this->title, $request->language);
        $relatedSkills = WikipediaHelper::fetchRelatedSkills(config('app.skills_recommendation_engine_url').strtolower($this->title));

        $data = [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $skillDescription !== false ? $skillDescription : '',
            'related_skills'=> $relatedSkills !== false ? $relatedSkills : [],
            'is_saved'      => (!empty(UserSkillsService::checkUserSkillExists($this->id))) ? "yes":"no",
            "related_jobs"=>[],
        ];
        if (isset($this->user_pinned->pinned)){
            $data['pinned']= $this->user_pinned->pinned==1 ? 'yes':'no';
        }
        return $data;
    }
}
