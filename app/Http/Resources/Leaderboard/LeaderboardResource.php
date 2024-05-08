<?php

namespace App\Http\Resources\Leaderboard;

use App\Http\Resources\User\UserResource;
use App\Services\UserPointService;
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
        $leaningPoints=UserPointService::getUserPoints($this->id);
        return [
            'user'                => UserResource::make($this),
            'leaning_points'      => $leaningPoints,
            'rank'                => $this->user_rank,
            'achievement_points'  =>count($this->userAchievements),
        ];
    }
}
