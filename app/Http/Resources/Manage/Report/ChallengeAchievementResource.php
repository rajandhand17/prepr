<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'achievement_type'      => $this->achievement_type == '0' ? 'Participation' : 'Incentive',
            'achievement_name'      => $this->achievement_name,
            'achievement_points'    => $this->achievement_points,
            'achievement_image'     => $this->achievement_image,
            'achievement_condition' => $this->achievement_condition,
        ];
    }
}
