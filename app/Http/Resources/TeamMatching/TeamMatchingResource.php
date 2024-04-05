<?php

namespace App\Http\Resources\TeamMatching;

use App\Helpers\UtilityHelper;
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
        if ($this->challenge_id) {
            $fetchChallenge = ChallengeService::getChallengeBasedOnId($this->challenge_id);
            if ($fetchChallenge) {
                $getTemplate = ($this->getProjectIdBasedTemplate !== null) ? $this->getProjectIdBasedTemplate->template_id : ($fetchChallenge->challenge_project_template->template_id ?? 0);
                $projectDate = UtilityHelper::formatDateTime($this->created_at);
                $fetchChallengeDueDate = ChallengeService::fetchChallengeDueDate($fetchChallenge, $projectDate);
                $challenge_details = [
                    'id' => $fetchChallenge->id,
                    'uuid' => $fetchChallenge->uuid,
                    'title' => $fetchChallenge->title,
                    'slug' => $fetchChallenge->slug,
                    'agreement' => $fetchChallenge->agreement,
                    'template_id' => $getTemplate,
                    'challenge_type' => $fetchChallengeDueDate['timeline_type'],
                    'due_date' => $fetchChallengeDueDate['submission_deadline_date'],
                    'submission_status' => $fetchChallengeDueDate['submission_status'],
                    'challenge_status' => $fetchChallengeDueDate['challenge_status'],
                ];
            }
        }
        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }
        $getUsersDetails=UserService::getUserById($this->user_id);
        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_details'          => UserResource::make($getUsersDetails),
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'challenge_title'       => $this->challenge->title,
            'member_count'          => $this->getMembersCount(),
            'challenge_details'     => $challenge_details,
            'skills'                => $skills,
        ];
    }
}
