<?php

namespace App\Http\Resources\Public\Skill;

use App\Helpers\UtilityHelper;
use App\Helpers\WikipediaHelper;
use App\Http\Resources\Career\CareerResource;
use App\Http\Resources\Public\Lab\LabResource;
use App\Services\JobTitleService;
use App\Services\JobTitleSkillServices;
use App\Services\Manage\LabService;
use App\Services\UserSkillsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => WikipediaHelper::fetchSkillDescription($this->title, $request->language) ?: '',
        ];

        if (auth('api')->check()) {
            $data['saved_on'] = $this->saved_skill->created_at ? UtilityHelper::formatDateTime($this->saved_skill->created_at) : null;
            $data['pinned'] = $this->user_pinned->pinned == '1' ? 'yes' : 'no';
        }

        return $data;
    }
}
