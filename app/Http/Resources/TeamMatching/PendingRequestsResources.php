<?php

namespace App\Http\Resources\TeamMatching;

use App\Services\DurationService;
use App\Services\LevelService;
use App\Services\Manage\ChallengeService;
use App\Services\ProjectService;
use App\Services\SkillService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendingRequestsResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skills = null;
        $userDetails = null;
        $getUsersDetails = UserService::getUserByEmail($this->email);
        if ($getUsersDetails) {
            $userDetails['id'] = $getUsersDetails['id'];
            $userDetails['full_name'] = $getUsersDetails['full_name'];
            $userDetails['username'] = $getUsersDetails['username'];
            $userDetails['email'] = $getUsersDetails['email'];
            $userDetails['profile_image'] = $getUsersDetails['profile_image'];
            $userDetails['user_points'] = $getUsersDetails['user_points'];
            $userDetails['user_rank'] = $getUsersDetails['user_rank'];
            $userDetails['verified_user'] = $getUsersDetails['verified_user'];
            $accessLevel = ['viewer', 'editor', 'team-lead'];
            $userDetails['project_count'] = count($getUsersDetails->userProjects);
            $userDetails['lab_count'] = count($getUsersDetails->userlabs);
            $userDetails['achievement_count'] = count($getUsersDetails->userAchievements);
            $userDetails['skill_count'] = count($getUsersDetails->userSkills);
            $userDetails['position'] = ($this->member !== null) ? $accessLevel[$this->member->inviter_access_level] : null;
            $userDetails['bio'] = $getUsersDetails->userPersonal !== null ? $getUsersDetails->userPersonal->about : null;
            $isJoined = 'no';
        }
        $access_level = 'viewer';
        $project = ProjectService::getProjectBasedOnId($this->project_id);
        if ($project->challenge_id) {
            $challenges = [];
            $challenge_details = ChallengeService::getChallengeDetailedBasedOnChallenges($project->challenge_id, $project->created_at, $project->getProjectIdBasedTemplate);
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

        if ($project->skills) {
            $associatedSkills = $project->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        switch ($this->invite_status) {
            case '0':
                $friendRequest = 'invited';
                break;
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
                break;
        }

        return [
            'id'                    => $project->uuid,
            'language'              => $project->language,
            'user_details'          => $userDetails,
            'media'                 => $project->media,
            'title'                 => $project->title,
            'slug'                  => $project->slug,
            'description'           => $project->description,
            'challenge_details'     => $challenges,
            'skills'                => $skills,
            'privacy'               => ($project->privacy == 0) ? 'Public' : 'Private',
            'request_send'          => $friendRequest,
            'is_joined'             => $isJoined,
            'access_level'          => $access_level,
        ];
    }
}
