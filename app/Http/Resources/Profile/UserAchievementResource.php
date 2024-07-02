<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        switch ($this->achievement_type) {
            case '0':
                $achievement_type = __('responses.user_achievement_type_lab');
                break;
            case '1':
                $achievement_type = __('responses.user_achievement_type_lab_program');
                break;
            case '2':
                $achievement_type = __('responses.user_achievement_type_challenge');
                break;
            case '3':
                $achievement_type = __('responses.user_achievement_type_challenge_path');
                break;
            case '4':
                $achievement_type = __('responses.user_achievement_type_resource_group');
                break;
            case '5':
                $achievement_type = __('responses.user_achievement_type_appreciation_award');
                break;
            case '6':
                $achievement_type = __('responses.user_achievement_type_activity_award');
                break;
            case '7':
                $achievement_type = __('responses.user_achievement_type_skill_activity');
                break;
            case '8':
                $achievement_type = __('responses.user_achievement_type_imported_award');
                break;
            case '9':
                $achievement_type = __('responses.user_achievement_type_winner_award');
                break;
            case '10':
                $achievement_type = __('responses.user_achievement_type_participation_award');
                break;
            default:
                $achievement_type = null;
                break;
        }

        return [
            'certificate_number' => $this->certificate_number,
            'title'              => $this->title,
            'description'        => $this->description,
            'module_id'          => $this->module_id,
            'module_title'       => $this->module_title,
            'achievement_type'   => $achievement_type,
            'module_parent_id'   => $this->module_parent_id,
            'module_parent_title'=> $this->module_parent_title,
            'achievement_prize'  => $this->achievement_prize,
            'achievement_points' => $this->achievement_points,
            'achievement_image'  => $this->achievement_image,
            'issue_date'         => $this->issue_date,
            'valid_date'         => $this->valid_date,
            'user_notified'      => $this->user_notified,
            'is_featured'        => ($this->is_featured == 1) ? 'yes' : 'no',
            'promo_code'         => $this->promo_code,
        ];
    }
}
