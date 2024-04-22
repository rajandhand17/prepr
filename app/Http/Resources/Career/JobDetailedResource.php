<?php

namespace App\Http\Resources\Career;

use App\Helpers\WikipediaHelper;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Master\SkillResource;
use App\Models\Skill;
use App\Services\Manage\ChallengeService;
use App\Services\SkillService;
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
        $getAllChallenges=$this->related_challenge->pluck('challenge_id')->take(4);
        if($getAllChallenges){
            $getChallenges=ChallengeService::getChallengeBasedOnIds($getAllChallenges);
        }

        $requiredSkills=SkillService::getSkills();
        dd($this->id,$getCurrentUsersSkills,count($getSkills));

        return [
            'id'          =>$this->id,
            'uuid'        =>$this->uuid,
            'title'       =>$this->title,
            'matched_skills'=>$this->skills,
            'description' =>WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'skills'      =>SkillResource::collection($this->skills),
            'lightcast_id'=>$this->lightcast_id,
            'challenges'  =>ChallengeResource::collection($getChallenges),
            'saved_on'    =>$this->created_on,
            'pinned'      =>$this->pinned,
        ];
    }
}
