<?php

namespace App\Http\Resources\TeamMatching;

use App\Http\Resources\User\UserResource;
use App\Services\Manage\ChallengeService;
use App\Services\SkillService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMatchingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $challenge_details = null;
        $skills = null;
        if ($this->challenge_id) {
            $challenge_details = ChallengeService::getChallengeDetailedBasedOnChallenges($this->challenge_id, $this->created_at, $this->getProjectIdBasedTemplate);
        }
        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }
        $getUsersDetails = UserService::getUserById($this->user_id);

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_details'          => UserResource::make($getUsersDetails),
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'challenge_title'       => $this->challenge !== null ? $this->challenge->title : null,
            'member_count'          => $this->getMembersCount(),
            'challenge_details'     => $challenge_details,
            'skills'                => $skills,
            'request_send'          => 'no',  //temporarily added for frontend design implementations
        ];
    }
}
