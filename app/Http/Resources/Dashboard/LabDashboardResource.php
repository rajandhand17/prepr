<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\Public\Airmeet\AirmeetEventResource;
use App\Http\Resources\Public\Organization\OrganizationHostResource;
use App\Services\AchievementConditionListService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class LabDashboardResource extends JsonResource
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
        $module_status = 'not_started';
        $module_progress = [
            'status'        => $module_status,
            'percentage'    => '0',
        ];
        if ($this->lab_completion_status) {
            switch ($this->lab_completion_status->status) {
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
                'percentage'    => $this->lab_completion_status->percentage,
            ];
        }

        return [
            'component'                     => 'lab',
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'description'                   => $this->description,
            'module_progress'               => $module_progress,
            'last_updated'                  => $this->updated_at,
        ];
    }
}
