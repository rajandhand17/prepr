<?php

namespace App\Http\Resources\TeamMatching;

use App\Services\DurationService;
use App\Services\LevelService;
use App\Services\Manage\ChallengeService;
use App\Services\ProjectMemberManagementService;
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
        $skills = [];
        $userDetails = null;
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
                $challenges['duration'] = DurationService::getDurationsBasedOnId($challenge_details['duration_id'])->title;
                $challenges['level'] = LevelService::getLevelsBasedOnId($challenge_details['level_id'])->title;
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
            $accessLevel = ['viewer', 'editor', 'team-lead'];
            if ($action == 'pending') {
                $userDetails['project_count'] = count($getUsersDetails->userProjects);
                $userDetails['lab_count'] = count($getUsersDetails->userlabs);
                $userDetails['achievement_count'] = count($getUsersDetails->userAchievements);
                $userDetails['skill_count'] = count($getUsersDetails->userSkills);
                $userDetails['position'] = $accessLevel[$this->member->inviter_access_level];
                $userDetails['bio'] = $getUsersDetails->userPersonal !== null ? $getUsersDetails->userPersonal->about : null;
            }
            $isJoined = 'no';
            if ($action == 'matched') {
                $isJoined = $this->member ? 'yes' : 'no';
                $userDetails['position'] = $accessLevel[$this->member->inviter_access_level];
            }
        }
        $friendRequest="";
        $getRequest = ProjectMemberManagementService::checkRequestExistsOrNotExists($this->id);
        if ($getRequest) {
            switch ($getRequest){
                case '1':
                    $friendRequest = 'joined';
                    break;
                case '2':
                    $friendRequest = 'pending';
                    break;
                case '3':
                    $friendRequest = 'available';
                    break;
                default:
                    $friendRequest = 'available';
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
            'request_send'          => $friendRequest,
            'is_joined'             => $isJoined,
        ];
    }
}
