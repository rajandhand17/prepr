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
        switch($this->achievement_type) {
            case '0':
                $activity = 'Lab';
                break;
            case '1':
                $activity = 'Lab Program';
                break;
            case '2':
                $activity = 'Challenge';
                break;
            case '3':
                $activity = 'Challenge Path';
                break;
            case '4':
                $activity = 'Resource Group';
                break;
            case '5':
                $activity = 'Appreciation Award';
                break;
            case '6':
                $activity = 'Activity Award';
                break;
            case '7':
                $activity = 'Skill Activity';
                break;
            case '8':
                $activity = 'Imported Award';
                break;
            case '9':
                $activity = 'Winner Award';
                break;
            case '10':
                $activity = 'Participation Award';
                break;
            default:
                $activity = null;
                break;
        }
        $request = [
            'id'                 => $this->id,
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
