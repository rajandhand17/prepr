<?php

namespace App\Http\Resources\Career;

use App\Helpers\UtilityHelper;
use App\Helpers\WikipediaHelper;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Services\JobTitleService;
use App\Services\JobTitleSkillServices;
use App\Services\Manage\ChallengeService;
use App\Services\Public\LabService;
use App\Services\Public\ResourceModuleService;
use App\Services\UserJobTitlesService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $getChallenges = null;
        $getAllLabs = null;
        $getAllChallenges = $this->related_challenge->pluck('challenge_id')->take(config('site-settings.jobs_details_par_module_limit'));
        if ($getAllChallenges) {
            $getChallenges = ChallengeService::getChallengeBasedOnIds($getAllChallenges);
        }
        $getAllLabs = $this->related_labs->pluck('lab_id')->take(config('site-settings.jobs_details_par_module_limit'));
        if ($getAllLabs) {
            $getAllLabs = LabService::getLabsBasedOnIds($getAllLabs);
        }
        $resources = [];
        $getResources = $this->related_resources;

        $getAllResources = $getResources->pluck('resource_module_id')->take(config('site-settings.jobs_details_par_module_limit'));
        if ($getAllResources) {
            $resources = ResourceModuleService::getResourceModuleBasedOnIds($getAllResources);
        }
        $getPercentageOfSkills = JobTitleSkillServices::getPercentagesOfMatchedSkills($this->id);
        $checkSavedOrNot = UserJobTitlesService::checkJobExistsOrNot($this->id);
        $saved = ($checkSavedOrNot == false) ? 'no' : 'yes';
        $pinned = 'no';
        if ($saved !== 'no') {
            $pinned = ($checkSavedOrNot->pinned == '1') ? 'yes' : 'no';
        }
        $saved_on = $this->created_at;
        if ($saved == 'yes') {
            $saved_on = $checkSavedOrNot->created_at;
        }
        $getRelatedJobs = JobTitleService::getRelatedJobs($this->id);
        $getTrendingJobs = JobTitleService::gettrendingJobs($this);
        $getJobLiveTrending = JobTitleService::getLiveJobs($this);

        return [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'title'            => $this->title,
            'related_skills'   => $this->skills,
            'description'      => WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'skills'           => SkillResource::collection($this->skills),
            'lightcast_id'     => $this->lightcast_id,
            'challenges'       => ChallengeResource::collection($getChallenges),
            'saved_on'         => UtilityHelper::formatDateTime($saved_on),
            'pinned'           => $pinned,
            'labs'             => LabResource::collection($getAllLabs),
            'resources'        => ResourceCollectionResource::collection($resources),
            'related_jobs'     => CareerResource::collection($getRelatedJobs),
            'live_jobs'        => $getJobLiveTrending['jobPostings'],
            'skills_percentage'=> intval($getPercentageOfSkills),
            'saved'            => $saved,
            'job_trends'       => $getTrendingJobs,
        ];
    }
}
