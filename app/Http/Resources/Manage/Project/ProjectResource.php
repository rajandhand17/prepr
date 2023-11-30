<?php

namespace App\Http\Resources\Manage\Project;

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
        $project_files = null;
        $project_requirement_status = null;

        if ($this->getProjectTemplate->getTemplatePitches) {
            $challenge_pitch = $this->getProjectTemplate->getTemplatePitches->map(function ($task) {
                $pitchAnswer = ProjectPitchService::getPitchAnswerBasedOnId($task, $this->id, $this->language);

                return [
                    'pitch_id'          => $pitchAnswer->id,
                    'title'             => $pitchAnswer->title,
                    'pitch_answer'      => $pitchAnswer->description_answer,
                ];
            });
        }

        if ($this->getProjectTemplate->getTemplateTasks) {
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

        if ($this->getProjectFile) {
            $project_files = $this->getProjectFile->map(function ($file) {
                return [
                    'id'        => $file->id,
                    'title'     => $file->title,
                    'path'      => $file->path,
                    'type'      => $file->type,
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
            $challengeData = ChallengeService::getChallengeBasedOnId($this->challenge_id)->only(['id', 'uuid', 'title', 'slug']);
        }

        if ($this->lab_id) {
            $labData = LabService::getLabBasedOnId($this->lab_id)->only(['id', 'uuid', 'title', 'slug']);
        }

        $project_requirement_status = ProjectService::projectRequirements($this);

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
            'status'                => $this->status,
            'challenge_id'          => $challengeData,
            'lab_id'                => $labData,
            'requirement_status'    => $project_requirement_status,
            'project_pitch'         => $challenge_pitch,
            'project_task'          => $challenge_task,
            'project_files'         => $project_files,
        ];
    }
}
