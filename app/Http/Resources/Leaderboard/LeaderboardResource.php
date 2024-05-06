<?php

namespace App\Http\Resources\Leaderboard;

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
            'id'                => $this->id,
            'leaning_points'    => $leaningPoints,
        ];
    }
}
