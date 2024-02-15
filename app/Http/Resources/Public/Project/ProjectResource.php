<?php

namespace App\Http\Resources\Public\Project;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ProjectPitchService;
use App\Services\Manage\ProjectService;
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
        $challengeData = null;
        $labData = null;
        $challenge_pitch = null;
        $challenge_task = null;
        $status = 'yes';
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

        switch ($this->view_enabled) {
            case 'yes':
                $view_enabled = 'yes';
                break;
            case 'no':
                $view_enabled = 'no';
                break;
            default:
                $view_enabled = 'yes';
                break;
        }

        switch ($this->download_enabled) {
            case 'yes':
                $download_enabled = 'yes';
                break;
            case 'no':
                $download_enabled = 'no';
                break;
            default:
                $download_enabled = 'yes';
                break;
        }

        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
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
                $challengeData = [
                    'id'                => $fetchChallenge->id,
                    'uuid'              => $fetchChallenge->uuid,
                    'title'             => $fetchChallenge->title,
                    'slug'              => $fetchChallenge->slug,
                    'challenge_type'    => $fetchChallengeDueDate['timeline_type'],
                    'due_date'          => $fetchChallengeDueDate['submission_deadline_date'],
                ];
            }
        }

        $joinedStatus = 'no';
        if ($this->getJoinedStatus()) {
            switch ($this->getJoinedStatus() !== null) {
                case '0':
                    $joinedStatus = 'invited';
                    break;
                case '1':
                    $joinedStatus = 'yes';
                    break;
                case '2':
                    $joinedStatus = 'pending';
                    break;
                case '3':
                    $joinedStatus = 'no';
                    break;
                default:
                    $joinedStatus = 'no';
                    break;
            }
        }

        if ($this->lab_id) {
            $labData = LabService::getLabBasedOnId($this->lab_id)->only(['id', 'uuid', 'title', 'slug']);
        }

        switch ($this->status) {
            case '0':
                $status = 'no';
                break;
            case '1':
                $status = 'yes';
                break;
            default:
                $status = 'yes';
                break;
        }

        if ($this->likes()) {
            $liked = $this->likes() > 0 ? 'yes' : 'no';
        }

        if ($this->votes()) {
            $voted = $this->votes() > 0 ? 'yes' : 'no';
        }

        $submitEnabled = ProjectService::checkProjectRequirementCompleted($this);

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_id'               => $this->user_id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'view_enabled'          => $view_enabled,
            'download_enabled'      => $download_enabled,
            'media_type'            => $this->media_type,
            'media'                 => $media,
            'status'                => $status,
            'liked'                 => $liked,
            'likes'                 => $this->likes(),
            'voted'                 => $voted,
            'votes'                 => $this->votes(),
            'shares'                => $this->shares(),
            'favourite'             => $this->favourite(),
            'member_count'          => $this->getMembersCount(),
            'joinedStatus'          => $joinedStatus,
            'challenge_id'          => $challengeData,
            'lab_id'                => $labData,
            'is_submitted'          => $this->is_submitted !== '0' ? 'yes' : 'no',
            'submitEnabled'         => $submitEnabled !== false ? 'yes' : 'no',
            'requirement_status'    => ProjectRequirementResource::make($this),
            'project_pitch'         => $challenge_pitch,
            'project_task'          => $challenge_task,
            'project_files'         => ProjectFileResource::make($this),
            'external_links'        => ProjectExternalLinkResource::collection($this->external_links),
            'additional_info'       => ProjectAdditionalInfoResource::make($this->getProjectAdditionalInfo),
            'updated_at'            => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
