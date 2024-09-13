<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Resources\Json\JsonResource;

class ChallengePitchTasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $challenge_pitch = null;
        $challenge_task = null;

        if ($this->challenge_pitch) {
            $challenge_pitch = $this->challenge_pitch->map(function ($pitch) {
                return [
                    'id'            => $pitch->id,
                    'title'         => $pitch->title,
                    'description'   => $pitch->description,
                ];
            });
        }

        if ($this->challenge_task) {
            $challenge_task = $this->challenge_task->map(function ($task) {
                return [
                    'id'            => $task->id,
                    'title'         => $task->title,
                    'description'   => $task->description,
                ];
            });
        }

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'challenge_pitch'   => $challenge_pitch,
            'challenge_task'    => $challenge_task,
        ];
    }
}
