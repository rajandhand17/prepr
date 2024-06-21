<?php

namespace App\Http\Resources\TeamMatching;

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
        $action = request()->route('action');
        $challenge_details = null;
        $skills = null;
        $userDetails = [];
        if ($this->challenge_id) {
            $challenges = [];
            $challenge_details = ChallengeService::getChallengeDetailedBasedOnChallenges($this->challenge_id, $this->created_at, $this->getProjectIdBasedTemplate);
            if ($challenge_details !== null) {
                $challenges['id'] = $challenge_details['id'];
                $challenges['uuid'] = $challenge_details['uuid'];
                $challenges['slug'] = $challenge_details['slug'];
                $challenges['template_title'] = $challenge_details['template_title'];
                $challenges['due_date'] = $challenge_details['due_date'];
                $challenges['challenge_status'] = $challenge_details['challenge_status'];
            }
        }
        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }
        $getUsersDetails = UserService::getUserById($this->user_id);
        if ($getUsersDetails) {
            $userDetails['id'] = $getUsersDetails['id'];
            $userDetails['full_name'] = $getUsersDetails['full_name'];
            $userDetails['username'] = $getUsersDetails['username'];
            $userDetails['profile_image'] = $getUsersDetails['profile_image'];
            $userDetails['user_points'] = $getUsersDetails['user_points'];
            $userDetails['user_rank'] = $getUsersDetails['user_rank'];
            $userDetails['verified_user'] = $getUsersDetails['verified_user'];
            $userDetails['sso_integrations'] = [
                'linked-in'     => 'inactive',
                'google'        => 'inactive',
                'magnet'        => 'inactive',
                'microsoft'     => 'inactive',
                'apple'         => 'inactive',
            ];
            if ($action == 'pending') {
                $userDetails['project_count'] = count($getUsersDetails->userProjects);
                $userDetails['lab_count'] = count($getUsersDetails->userlabs);
                $userDetails['achievement_count'] = count($getUsersDetails->userAchievements);
                $userDetails['skill_count'] = count($getUsersDetails->userSkills);
            }
        }

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_details'          => $userDetails,
            'media'                 => $this->media,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'challenge_title'       => $this->challenge !== null ? $this->challenge->title : null,
            'member_count'          => $this->getMembersCount(),
            'challenge_details'     => $challenges,
            'skills'                => $skills,
            'privacy'               => ($this->privacy == 0) ? 'Public' : 'Private',
            'request_send'          => $this->friendRequest == null ? 'available' : 'request_sent',

        ];
    }
}
