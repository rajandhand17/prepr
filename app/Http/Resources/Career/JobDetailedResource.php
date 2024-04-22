<?php

namespace App\Http\Resources\Career;

use App\Helpers\WikipediaHelper;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Master\SkillResource;
use App\Services\Manage\ChallengeService;
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
        $getAllchallenges=$this->related_challenge->pluck('challenge_id')->take(4);
        if($getAllchallenges){
            $getChallenges=ChallengeService::getChallengeBasedOnIds($getAllchallenges);
        }
        return [
            'id'          =>$this->id,
            'uuid'        =>$this->uuid,
            'title'       =>$this->title,
            'description' =>WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'skills'      =>SkillResource::collection($this->skills),
            'lightcast_id'=>$this->lightcast_id,
            'challenges'  =>$getChallenges!=='' ? ChallengeResource::collection($getChallenges):[],
            'saved_on'    =>$this->created_on,
            'pinned'      =>$this->pinned,
        ];
    }
}
