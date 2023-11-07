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
                $achievement_type = 'Lab';
                break;
            case '1':
                $achievement_type = 'Lab Program';
                break;
            case '2':
                $achievement_type = 'Challenge';
                break;
            case '3':
                $achievement_type = 'Challenge Path';
                break;
            case '4':
                $achievement_type = 'Resource Group';
                break;
            case '5':
                $achievement_type = 'Appreciation Award';
                break;
            case '6':
                $achievement_type = 'Activity Award';
                break;
            case '7':
                $achievement_type = 'Skill Activity';
                break;
            case '8':
                $achievement_type = 'Imported Award';
                break;
            case '9':
                $achievement_type = 'Winner Award';
                break;
            case '10':
                $achievement_type = 'Participation Award';
                break;
            default:
                $achievement_type = null;
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
            'achievement_type'   => $achievement_type,
        ];

        return $request;
    }
}
