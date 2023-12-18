<?php

namespace App\Http\Resources\Manage\Lab;

use App\Helpers\UtilityHelper;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class LabTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'description'                   => $this->description,
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'total_share'                   => $this->total_share,
            'is_auto_created'               => ($this->is_auto_created == '1') ? 'yes' : 'no',
            'is_resource_sequential'        => ($this->is_resource_sequential == '1') ? 'yes' : 'no',
            'is_sequential'                 => ($this->is_sequential == '1') ? 'yes' : 'no',
            'is_achievement_enabled'        => ($this->is_achievement_enabled == '1') ? 'yes' : 'no',
            'is_notification_enabled'       => ($this->is_notification_enabled == '1') ? 'yes' : 'no',
            'is_verified'                   => ($this->is_verified == '1') ? 'yes' : 'no',
            'lab_program_count'             => 0,
            'challenge_count'               => 0,
            'challenge_path_count'          => 0,
            'resource_module_count'         => 0,
            'resource_collection_count'     => 0,
            'resource_group_count'          => 0,
            'last_updated'                  => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
