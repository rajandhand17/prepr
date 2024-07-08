<?php

namespace App\Http\Resources\Leaderboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    private static $rankCounter = 1;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id,
            'full_name'                   => $this->full_name,
            'username'                    => $this->username,
            'rank'                        => self::$rankCounter++,
            'profile_image'               => $this->profile_image,
            'learning_points'             => $this->user_points !== null ? $this->user_points : 0, //temporarily added
            'learning_rank'               => $this->user_rank !== null ? $this->user_rank : 0,
            'achievement_points'          => $this->achievement_count ? $this->achievement_count : 0,  //temporarily added
        ];
    }

    public static function resetRankCounter()
    {
        self::$rankCounter = 1;
    }
}
