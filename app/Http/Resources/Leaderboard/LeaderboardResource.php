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
            'id'                          => $this->id,
            'preferred_language'          => $this->preferred_language,
            'preferred_timezone'          => $this->preferred_timezone ? $this->preferred_timezone : 'EST',
            'first_name'                  => $this->first_name,
            'last_name'                   => $this->last_name,
            'full_name'                   => $this->full_name,
            'username'                    => $this->username,
            'email'                       => $this->email,
            'phone_number'                => $this->phone_number,
            'profile_image'               => $this->profile_image,
            'leaning_points'              => $this->user_points,
            'rank'                        => $this->user_rank,
            'achievement_points'          => $this->achievement_count,
        ];
    }
}
