<?php

namespace App\Http\Resources\Public\Achievement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $activity = null;
        switch($this->achievement_type) {
            case '0':
                $activity = 'lab';
                break;
            case '1':
                $activity = 'labprogram';
                break;
            case '2':
                $activity = 'challenge';
                break;
            case '3':
                $activity = 'challengepath';
                break;
            case '4':
                $activity = 'resourcegroup';
                break;
            case '5':
                $activity = 'appreciation';
                break;
            case '6':
                $activity = 'activity';
                break;
            case '7':
                $activity = 'skillactivity';
                break;
            case '8':
                $activity = 'imported';
                break;
            case '9':
                $activity = 'winner';
                break;
            case '10':
                $activity = 'participation';
                break;
            default:
                $activity = null;
                break;
        }
        $request = [
            'id'                 => $this->certificate_number,
            'title'              => $this->title,
            'description'        => $this->description,
            'module_title'       => $this->module_title,
            'module_parent_title'=> $this->module_parent_title,
            'achievement_prize'  => $this->achievement_prize,
            'achievement_points' => $this->achievement_points,
            'achievement_image'  => $this->achievement_image,
            'promo_code'         => $this->promo_code,
            'achievement_type'   => $activity,
        ];

        return $request;
    }
}
