<?php

namespace App\Http\Resources\TeamMatching;

use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Master\SkillResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendingRequestsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name'=>$this->full_name,
            'description'=>$this->description,
//            'media'=>$this->media,
            'skills'=>SkillResource::collection($this->userSkills),
            'skill_count'=>count($this->userSkills),
            'labs_count'=>count($this->userLabs),
            'projects_count'=>count($this->userProjects),
            'achievements_count'=>count($this->userAchievements),
//            'achievements'=>'',
//            'projects'=>'',
        ];
    }
}
