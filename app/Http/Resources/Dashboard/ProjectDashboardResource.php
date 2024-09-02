<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        switch ($this->is_submitted) {
            case '0':
                $project_status = 'in_progress';
                break;

            case '1':
                $project_status = 'completed';
                break;

            case '2':
                $project_status = 'completed';
                break;
        }

        return [
            'component'             => 'project',
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'slug'                  => $this->slug,
            'title'                 => $this->title,
            'description'           => $this->description,
            'module_progress'       => $project_status,
            'updated_at'            => $this->updated_at,
        ];
    }
}
