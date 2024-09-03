<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectAudioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $project_files = null;

        if ($this->getProjectAudios) {
            $project_files = $this->getProjectAudios->map(function ($file) {
                return [
                    'id'        => $file->id,
                    'title'     => $file->title,
                    'path'      => $file->path,
                    'type'      => $file->type,
                ];
            });
        }

        return $project_files;
    }
}
