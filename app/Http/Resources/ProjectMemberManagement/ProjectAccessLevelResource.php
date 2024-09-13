<?php

namespace App\Http\Resources\ProjectMemberManagement;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectAccessLevelResource extends JsonResource
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
        return [
            'name' => $this->display_name,
        ];
    }
}
