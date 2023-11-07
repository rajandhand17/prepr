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
        $activity=null;
        switch($this->achievement_type) {
            case '0':
                $activity = __("responses.switch_type_lab");
                break;
            case '1':
                $activity = __("responses.switch_type_lab_program");
                break;
            case '2':
                $activity = __("responses.switch_type_challenge");
                break;
            case '3':
                $activity = __("responses.switch_type_challenge_path");
                break;
            case '4':
                $activity = __("responses.switch_type_resource_group");
                break;
            case '5':
                $activity = __("responses.switch_type_appreciation");
                break;
            case '6':
                $activity =__("responses.switch_type_activity");
                break;
            case '7':
                $activity =__("responses.switch_type_skill");
                break;
            case '8':
                $activity = __("responses.switch_type_imported");
                break;
            case '9':
                $activity =__("responses.switch_type_winner");
                break;
            case '10':
                $activity =__("responses.switch_type_participation");
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
