<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDashboardLayoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        switch ($this->card_type) {
            case '0':
                $cardType = 'reports';
                break;

            case '1':
                $cardType = 'deadlines';
                break;

            case '2':
                $cardType = 'leaderboard';
                break;

            case '3':
                $cardType = 'my-challenges';
                break;

            case '4':
                $cardType = 'my-labs';
                break;

            case '5':
                $cardType = 'my-projects';
                break;

            case '6':
                $cardType = 'my-resources';
                break;

            case '7':
                $cardType = 'my-organizations';
                break;

            case '8':
                $cardType = 'subscription';
                break;

            case '9':
                $cardType = 'inbox-friends';
                break;

            case '10':
                $cardType = 'recommendations';
                break;

            case '11':
                $cardType = 'continue-left';
                break;

            case '12':
                $cardType = 'achievement';
                break;
        }

        return [
            'dashboard_type'        => 'user',
            'card_type'             => $cardType,
            'is_active'             => $this->is_active == '0' ? 'yes' : 'no',
            'position_x'            => $this->position_x,
            'position_y'            => $this->position_y,
        ];
    }
}
