<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\Manage\Scorm\ScormResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayal|\JsonSerializable
     */
    public function toArray($request)
    {
        $module_status = 'not_started';
        $module_progress = [
            'status'        => $module_status,
            'percentage'    => '0',
        ];

        if ($this->challenge_completion_status) {
            switch ($this->challenge_completion_status->status) {
                case '0':
                    $module_status = 'not_started';
                    break;
                case '1':
                    $module_status = 'in_progress';
                    break;
                case '2':
                    $module_status = 'completed';
                    break;
            }

            $module_progress = [
                'status'        => $module_status,
                'percentage'    => $this->challenge_completion_status->percentage,
            ];
        }

        return [
            'component'                         => 'challenge',
            'id'                                => $this->uuid,
            'language'                          => $this->language,
            'slug'                              => $this->slug,
            'title'                             => $this->title,
            'description_type'                  => $this->description_type == '1' ? 'scorm' : 'text',
            'description'                       => $this->description,
            'scorm'                             => new ScormResource($this->scorm),
            'module_progress'                   => $module_progress,
            'scorm_url'                         => $this->formatted_scorm_url,
            'last_updated'                      => $this->updated_at,
        ];
    }
}
