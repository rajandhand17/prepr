<?php

namespace App\Http\Resources\Career;

use App\Helpers\WikipediaHelper;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Public\LabService;
use App\Services\UserSkillsService;
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

        $getResources = $this->related_resources;
        $getAllResources = $getResources->pluck('resource_collection_id')->take(config('site-settings.jobs_details_par_module_limit'));
        if ($getAllResources) {
            $resources = ResourceCollectionService::getResourceCollectionsBasedOnIds($getAllResources);
        }
        $requiredSkills = UserSkillsService::getUserSkills();

        return [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'title'         => $this->title,
            'matched_skills'=> $this->skills,
            'description'   => WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'skills'        => SkillResource::collection($this->skills),
            'lightcast_id'  => $this->lightcast_id,
            'challenges'    => ChallengeResource::collection($getChallenges),
            'saved_on'      => $this->created_on,
            'pinned'        => $this->pinned,
            'labs'          => LabResource::collection($getAllLabs),
            'resources'     => ResourceCollectionResource::collection($resources),
            'related_jobs'  => $this->related_jobs,
            'live_jobs'     => $this->job_posting,
        ];
    }
}
