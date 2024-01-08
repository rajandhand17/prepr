<?php

namespace App\Http\Resources\Manage\Project;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectFileResource extends JsonResource
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

        return $project_files;
    }
}
