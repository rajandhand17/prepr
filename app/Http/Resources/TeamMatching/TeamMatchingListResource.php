<?php

namespace App\Http\Resources\TeamMatching;

use App\Services\Manage\ChallengeService;
use App\Services\SkillService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMatchingListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skills = null;
        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }
        if ($this->challenge_id) {
            $challenge_details = ChallengeService::getChallengeBasedOnId($this->challenge_id);
            if ($challenge_details) {
                $challenges = [];
                $challenges['title'] = $challenge_details->title;
                $challenges['slug'] = $challenge_details->slug;
                $fetchChallengeDueDate = ChallengeService::fetchChallengeDueDate($challenge_details, $challenge_details->created_at);
                $challenges['due_date'] = $fetchChallengeDueDate['submission_deadline_date'];
            }
        }

        return [
            'title'               => $this->title,
            'media'               => $this->media,
            'privacy'             => ($this->privacy == 0) ? 'Public' : 'Private',
            'challenge_title'     => $challenges['title'],
            'challenge_slug'      => $challenges['slug'],
            'due_date'            => $challenges['due_date'],
            'skills'              => $skills,
        ];
    }
}
