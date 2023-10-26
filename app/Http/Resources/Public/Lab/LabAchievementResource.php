<?php

namespace App\Http\Resources\Public\Lab;

use Illuminate\Http\Resources\Json\JsonResource;

class LabAchievementResource extends JsonResource
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
            'id'                    => $this->id,
            'achievement_name'      => $this->achievement_name,
            'achievement_points'    => $this->achievement_points,
            'achievement_image'     => $this->achievement_image,
            'achievement_condition' => $this->achievement_condition,
        ];
    }
}
