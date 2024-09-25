<?php

namespace App\Http\Resources\Project;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\ProjectPitchService;
use App\Services\ProjectService;
use App\Services\SkillService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $view_enabled = null;
        $download_enabled = null;
        $challenge_details = null;
        $lab_details = null;
        $challenge_pitch = null;
        $challenge_task = null;
        $skills = null;
        $achievement = null;
        $privacy = 'no';
        $liked = 'no';
        $voted = 'no';
        $project_role = 'none';
        $is_assess_enabled = 'yes';

        switch ($this->privacy) {
            case '0':
                $privacy = 'no';
                break;
            case '1':
                $privacy = 'yes';
                break;
            default:
                $privacy = 'no';
                break;
        }
        if ($this->getProjectTemplate) {
            if ($this->getProjectTemplate->template_id == '0') {
                $templateData = $this->getProjectIdBasedTemplate ?? $this->getProjectTemplate;
            } else {
                $templateData = $this->getProjectTemplate;
            }
        }
        if ($templateData) {
            $challenge_pitch = $templateData->getTemplatePitches->map(function ($task) {
                $pitchAnswer = ProjectPitchService::getPitchAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'id'                => $pitchAnswer->id,
                    'title'             => $pitchAnswer->title,
                    'answer'            => $pitchAnswer->description_answer,
                ];
            });

            $challenge_task = $templateData->getTemplateTasks->map(function ($task) {
                $taskAnswer = ProjectPitchService::getTaskAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'id'                => $taskAnswer->id,
                    'title'             => $taskAnswer->title,
                    'answer'            => $taskAnswer->is_completed,
                    'task_completed_at' => $taskAnswer->completed_at,
                ];
            });
        }

        switch ($this->is_view_enabled) {
            case '0':
                $view_enabled = 'no';
                break;
            case '1':
                $view_enabled = 'yes';
                break;
            default:
                $view_enabled = 'no';
                break;
        }

        switch ($this->is_download_enabled) {
            case '0':
                $download_enabled = 'no';
                break;
            case '1':
                $download_enabled = 'yes';
                break;
            default:
                $download_enabled = 'no';
                break;
        }

        switch ($this->media_type) {
            case '0':
                $media = $this->media;
                $media_type = 'image';
                break;
            case '1':
                $media = $this->getRawOriginal('media');
                $media_type = 'embedded';
                break;
            case '2':
                $media = $this->media;
                $media_type = 'video';
                break;
            default:
                $media = $this->media;
                $media_type = 'image';
                break;
        }

        if ($this->challenge_id) {
            $challenge_details = ChallengeService::getChallengeDetailedBasedOnChallenges($this->challenge_id, $this->created_at, $templateData);
            $fetchChallenge = ChallengeService::getChallengeBasedOnId($this->challenge_id);
            if ($fetchChallenge) {
                $org = OrganizationService::getOrganizationExistBasedOnId($fetchChallenge->organization_id);
            }
            if ($fetchChallenge && $fetchChallenge->participation_achievement) {
                $achievement = [
                    'achievement_name'      => $fetchChallenge->participation_achievement->achievement_name,
                    'achievement_points'    => $fetchChallenge->participation_achievement->achievement_points,
                    'achievement_image'     => $fetchChallenge->participation_achievement->achievement_image,
                    'achievement_prize'     => $fetchChallenge->participation_achievement->achievement_prize,
                ];
            }

            if ($this->is_submitted == '1') {
                $is_assess_enabled = ($fetchChallenge && $fetchChallenge->is_open == '2') ? 'no' : 'yes';
            }
        }

        $joined_status = 'no';
        if ($this->getJoinedStatus()) {
            switch ($this->getJoinedStatus() !== null) {
                case '0':
                    $joined_status = 'invited';
                    break;
                case '1':
                    $joined_status = 'accepted';
                    break;
                case '2':
                    $joined_status = 'pending';
                    break;
                case '3':
                    $joined_status = 'declined';
                    break;
                default:
                    $joined_status = 'no';
                    break;
            }
        }

        $access_level = 'viewer';
        if ($this->getJoinedStatus() !== null && $this->getJoinedStatus()->invite_status == '1') {
            switch ($this->getJoinedStatus()->inviter_access_level) {
                case '0':
                    $access_level = 'viewer';
                    break;
                case '1':
                    $access_level = 'editor';
                    break;
                case '2':
                    $access_level = 'team_leader';
                    break;
                default:
                    $access_level = 'viewer';
                    break;
            }
        }

        if ($this->lab_id) {
            $lab_details = LabService::getLabBasedOnId($this->lab_id);
            if ($lab_details) {
                $lab_details = $lab_details->only(['id', 'uuid', 'title', 'slug', 'description', 'media', 'media_type', 'privacy', 'status']);
            }
        }

        if ($this->likes()) {
            $liked = $this->likes() > 0 ? 'yes' : 'no';
        }

        if ($this->votes()) {
            $voted = $this->votes() > 0 ? 'yes' : 'no';
        }

        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('skill_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        $submit_enabled = ProjectService::checkProjectRequirementCompleted($this);

        $project_role = 'none';
        if (auth('api')->check()) {
            $project_role = ProjectService::checkProjectRole($this, auth('api')->user());
        }
        // Extracting media collections from resources
        $imagesCollection = ProjectImageResource::make($this)->toArray(request());
        $videosCollection = ProjectVideoResource::make($this)->toArray(request());
        $audiosCollection = ProjectAudioResource::make($this)->toArray(request());
        $docsCollection = ProjectDocResource::make($this)->toArray(request());

        $images_count = count(ProjectImageResource::make($this)->toArray(request()));
        // Counting total files
        $files_count = count($videosCollection) + count($audiosCollection) + count($docsCollection);

        switch ($this->is_submitted) {
            case '0':
                $project_status = 'In Progress';
                break;

            case '1':
                $project_status = 'Submitted';
                break;

            case '2':
                $project_status = 'Late Submitted';
                break;

            default:
                $project_status = 'In Progress';
                break;
        }

        $created_by = [];
        if (auth('api')->check() && !empty($this->user_id)) {
            $userDetails = UserService::getUserById($this->user_id);
            $created_by['full_name'] = $userDetails->full_name;
            $created_by['username'] = $userDetails->username;
            $created_by['email'] = $userDetails->email;
            $created_by['profile_image'] = $userDetails->profile_image;
        }

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'created_by'            => $created_by,
            'user_id'               => auth('api')->check() ? $this->user_id : null,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'organization_id'       => (!empty($org->uuid)) ? $org->uuid : null,
            'organization'          => (!empty($org->title)) ? $org->title : null,
            'is_view_enabled'       => $view_enabled,
            'is_download_enabled'   => $download_enabled,
            'media_type'            => $media_type,
            'media'                 => $media,
            'privacy'               => $privacy,
            'liked'                 => auth('api')->check() ? $liked : null,
            'likes'                 => auth('api')->check() ? $this->likes() : null,
            'voted'                 => auth('api')->check() ? $voted : null,
            'votes'                 => auth('api')->check() ? $this->votes() : null,
            'shares'                => auth('api')->check() ? $this->shares() : null,
            'favourite'             => auth('api')->check() ? $this->favourite() : null,
            'access_level'          => auth('api')->check() ? $access_level : null,
            'is_submitted'          => auth('api')->check() ? ($this->is_submitted !== '0' ? 'yes' : 'no') : null,
            'project_status'        => auth('api')->check() ? $project_status : null,
            'submit_enabled'        => auth('api')->check() ? ($submit_enabled !== false ? 'yes' : 'no') : null,
            'project_role'          => auth('api')->check() ? $project_role : null,
            'recruiting_status'     => auth('api')->check() ? ($this->recruiting_status !== '0' ? 'no' : 'yes') : null,
            'member_count'          => $this->getMembersCount(),
            'joined_status'         => auth('api')->check() ? $joined_status : null,
            'members'               => ProjectMemberResource::collection($this->members),
            'skills'                => $skills,
            'challenge_details'     => $challenge_details,
            'challenge_achievement' => auth('api')->check() ? $achievement : null,
            'lab_details'           => $lab_details,
            'requirement_status'    => ProjectRequirementResource::make($this),
            'project_pitch'         => auth('api')->check() ? $challenge_pitch : null,
            'project_task'          => auth('api')->check() ? $challenge_task : null,
            'docs'                  => auth('api')->check() ? $docsCollection : null,
            'images'                => auth('api')->check() ? $imagesCollection : null,
            'images_count'          => "You've ".$images_count.' '.($images_count > 1 ? 'images uploaded' : 'image uploaded'), // Adding image count
            'files_count'           => "You've ".$files_count.' '.($files_count > 1 ? 'files uploaded' : 'file uploaded'),
            'videos'                => auth('api')->check() ? $videosCollection : null,
            'audios'                => auth('api')->check() ? $audiosCollection : null,
            'external_links'        => ProjectExternalLinkResource::collection($this->external_links),
            'is_assess_enabled'     => auth('api')->check() ? $is_assess_enabled : null,
            'additional_info'       => auth('api')->check() ? ProjectAdditionalInfoResource::make($this->getProjectAdditionalInfo) : null,
            'assessment_data'       => auth('api')->check() ? AssessedProjectResource::make($this) : null,
            'history'               => auth('api')->check() ? ProjectHistoryResource::collection($this->history) : null,
            'updated_at'            => $this->updated_at,
        ];
    }
}
