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
                $module_progress = [
                    'status'        => 'in_progress',
                    'percentage'    => '50',
                ];
                break;
            case '1':
            case '2':
                $module_progress = [
                    'status'        => 'completed',
                    'percentage'    => '100',
                ];
                break;
        }

        return [
            'component'             => 'project',
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'slug'                  => $this->slug,
            'title'                 => $this->title,
            'description'           => $this->description,
            'module_progress'       => $module_progress,
            'updated_at'            => $this->updated_at,
        ];
    }
}
