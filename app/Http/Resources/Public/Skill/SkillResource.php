<?php

namespace App\Http\Resources\Public\Skill;

use App\Helpers\UtilityHelper;
use App\Helpers\WikipediaHelper;
use App\Http\Resources\Career\CareerResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Services\JobTitleService;
use App\Services\JobTitleSkillServices;
use App\Services\Manage\LabService;
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
//        $relatedSkills = WikipediaHelper::fetchRelatedSkills(config('wikipedia.SKILLS_RECOMMENDATION_ENGINE_URL').strtolower($this->title));
//        $key = gettype($relatedSkills) == 'array' ? array_keys($relatedSkills) : [];
//        $count_related_skills = (is_array($relatedSkills)) ? count($relatedSkills) : '0';
//        $relatedKeyUrl = isset($key[0]) ? config('wikipedia.WIKIPEDIA_URL').str_replace(' ', '_', $key[0]) : [];
//        $getJobIds = JobTitleSkillServices::getJobTitleBasedOnSkills([$this->id]);
//        $getJobIdsBasedOnSkills = JobTitleService::getJobTitles('en', $this->search, $getJobIds)->take(config('site-settings.skills_par_module_limit'));
//        $getLabIdsBasedOnSKills = LabService::getLabBasedOnSkills($this->id);
        $data = [
            'id'                     => $this->id,
            'title'                  => $this->title,
            //        'description'            => $skillDescription !== false ? $skillDescription : '',
            //            'related_skills'         => $key !== false ? $key : [],
            //            'skill_url'              => $relatedKeyUrl,
            //            'is_saved'               => (!empty(UserSkillsService::checkUserSkillExists($this->id))) ? 'yes' : 'no',
            //            'related_skills_count'   => $count_related_skills,
            'related_challenges'     => $this->getChallenges != null ? $this->getChallenges->count() : '0',
            'related_labs_count'     => $this->getLabs != null ? $this->getLabs->count() : '0',
            'related_resources'      => $this->getLlatedResources != null ? $this->getLlatedResources->count() : '0',
            'related_jobs'           => CareerResource::collection($getJobIdsBasedOnSkills),
            'related_labs'           => LabResource::collection($getLabIdsBasedOnSKills),
        ];
        if (auth('api')->check()) {
            $data['saved_on'] = isset($this->saved_skill->created_at) ? UtilityHelper::formatDateTime($this->saved_skill->created_at) : null;

            if (isset($this->user_pinned->pinned)) {
                $data['pinned'] = $this->user_pinned->pinned == '1' ? 'yes' : 'no';
            }
        }

        return $data;
    }
}
