<?php

namespace App\Http\Resources\Leaderboard;

use App\Http\Resources\User\UserResource;
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
            'user'                => UserResource::make($this),
            'leaning_points'      => $this->user_points,
            'rank'                => $this->user_rank,
            'achievement_points'  => $this->achievement_count,
        ];
    }
}
