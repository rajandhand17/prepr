<?php

namespace App\Http\Resources\Leaderboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                           => $this->id,
            'username'                     => $this->username,
            'phone_number'                 => $this->phone_number,
            'profile_image'                => $this->profile_image,
            'learning_points'              => $this->user_points !== null ? $this->user_points : 90,
            'ranks'                        => $this->user_rank !== null ? $this->user_rank : 90,
            'achievement_points'           => $this->achievement_count !== null ? $this->achievement_count : 90,
        ];
    }
}
