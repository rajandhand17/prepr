<?php

namespace App\Http\Resources\Manage\Project;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\ProjectPitchService;
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

        if ($this->getProjectTemplate->getTemplatePitches) {
            $challenge_pitch = $this->getProjectTemplate->getTemplatePitches->map(function ($task) {
                $pitchAnswer = ProjectPitchService::getPitchAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'pitch_id'          => $pitchAnswer->id,
                    'title'             => $pitchAnswer->title,
                    'pitch_answer'      => $pitchAnswer->descriptionAnswer,
                ];
            });
        }

        if ($this->getProjectTemplate->getTemplateTasks) {
            $challenge_task = $this->getProjectTemplate->getTemplateTasks->map(function ($task) {
                $taskAnswer = ProjectPitchService::getTaskAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'task_id'           => $taskAnswer->id,
                    'title'             => $taskAnswer->title,
                    'task_answer'       => $taskAnswer->isCompleted,
                    'task_completed_at' => $taskAnswer->completedAt,
                ];
            });
        }

        switch ($this->view_enabled) {
            case '1':
                $view_enabled = 'yes';
                break;
            case '0':
                $view_enabled = 'no';
                break;
            default:
                $view_enabled = 'yes';
                break;
        }

        switch ($this->download_enabled) {
            case '1':
                $download_enabled = 'yes';
                break;
            case '0':
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
            $challengeData = ChallengeService::getChallengeBasedOnId($this->challenge_id)->only(['id', 'uuid', 'title', 'slug']);
        }

        if ($this->lab_id) {
            $labData = LabService::getLabBasedOnId($this->lab_id)->only(['id', 'uuid', 'title', 'slug']);
        }

        return [
            'id'                => $this->uuid,
            'language'          => $this->language,
            'user_id'           => $this->user_id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'view_enabled'      => $view_enabled,
            'download_enabled'  => $download_enabled,
            'media_type'        => $this->media_type,
            'media'             => $media,
            'status'            => $this->status,
            'challenge_id'      => $challengeData,
            'lab_id'            => $labData,
            'challenge_pitch'   => $challenge_pitch,
            'challenge_task'    => $challenge_task,
        ];
    }
}
