<?php

namespace App\Http\Resources\Project;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\ProjectPitchService;
use App\Services\ProjectService;
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
        $privacy = 'no';
        $liked = 'no';
        $voted = 'no';

        if ($this->getProjectTemplate) {
            $challenge_pitch = $this->getProjectTemplate->getTemplatePitches->map(function ($task) {
                $pitchAnswer = ProjectPitchService::getPitchAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'pitch_id'          => $pitchAnswer->id,
                    'title'             => $pitchAnswer->title,
                    'pitch_answer'      => $pitchAnswer->description_answer,
                ];
            });
        }

        if ($this->getProjectTemplate) {
            $challenge_task = $this->getProjectTemplate->getTemplateTasks->map(function ($task) {
                $taskAnswer = ProjectPitchService::getTaskAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'task_id'           => $taskAnswer->id,
                    'title'             => $taskAnswer->title,
                    'task_answer'       => $taskAnswer->is_completed,
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
                break;
            case '1':
                $media = $this->getRawOriginal('media');
                break;
            case '2':
                $media = $this->media;
                break;
            default:
                $media = $this->media;
                break;
        }

        if ($this->challenge_id) {
            $fetchChallenge = ChallengeService::getChallengeBasedOnId($this->challenge_id);
            if ($fetchChallenge) {
                $projectDate = UtilityHelper::formatDateTime($this->created_at);
                $fetchChallengeDueDate = ChallengeService::fetchChallengeDueDate($fetchChallenge, $projectDate);
                $challenge_details = [
                    'id'                => $fetchChallenge->id,
                    'uuid'              => $fetchChallenge->uuid,
                    'title'             => $fetchChallenge->title,
                    'slug'              => $fetchChallenge->slug,
                    'challenge_type'    => $fetchChallengeDueDate['timeline_type'],
                    'due_date'          => $fetchChallengeDueDate['submission_deadline_date'],
                ];
            }
        }

        $joined_status = 'no';
        if ($this->getJoinedStatus()) {
            switch ($this->getJoinedStatus() !== null) {
                case '0':
                    $joined_status = 'invited';
                    break;
                case '1':
                    $joined_status = 'yes';
                    break;
                case '2':
                    $joined_status = 'pending';
                    break;
                case '3':
                    $joined_status = 'no';
                    break;
                default:
                    $joined_status = 'no';
                    break;
            }
        }

        $access_level = 'viewer';
        if ($this->getJoinedStatus() !== null && $this->getJoinedStatus()->invite_status === '1') {
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
            $lab_details = LabService::getLabBasedOnId($this->lab_id)->only(['id', 'uuid', 'title', 'slug']);
        }

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

        if ($this->likes()) {
            $liked = $this->likes() > 0 ? 'yes' : 'no';
        }

        if ($this->votes()) {
            $voted = $this->votes() > 0 ? 'yes' : 'no';
        }

        $submit_enabled = ProjectService::checkProjectRequirementCompleted($this);

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_id'               => $this->user_id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'is_view_enabled'       => $view_enabled,
            'is_download_enabled'   => $download_enabled,
            'media_type'            => $this->media_type,
            'media'                 => $media,
            'privacy'               => $privacy,
            'liked'                 => $liked,
            'likes'                 => $this->likes(),
            'voted'                 => $voted,
            'votes'                 => $this->votes(),
            'shares'                => $this->shares(),
            'favourite'             => $this->favourite(),
            'member_count'          => $this->getMembersCount(),
            'joined_status'         => $joined_status,
            'access_level'          => $access_level,
            'challenge_details'     => $challenge_details,
            'lab_details'           => $lab_details,
            'is_submitted'          => $this->is_submitted !== '0' ? 'yes' : 'no',
            'submit_enabled'        => $submit_enabled !== false ? 'yes' : 'no',
            'requirement_status'    => ProjectRequirementResource::make($this),
            'project_pitch'         => $challenge_pitch,
            'project_task'          => $challenge_task,
            'project_files'         => ProjectFileResource::make($this),
            'external_links'        => ProjectExternalLinkResource::collection($this->external_links),
            'additional_info'       => ProjectAdditionalInfoResource::make($this->getProjectAdditionalInfo),
            'assessment_data'       => AssessedProjectResource::make($this),
            'updated_at'            => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
