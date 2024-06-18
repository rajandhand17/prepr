<?php

namespace App\Http\Resources\Public\Skill;

use App\Helpers\WikipediaHelper;
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
            'is_saved'               => (!empty(UserSkillsService::checkUserSkillExists($this->id))) ? 'yes' : 'no',
        ];

        if (auth('api')->check()) {
            if (isset($this->user_pinned->pinned)) {
                $data['pinned'] = $this->user_pinned->pinned == '1' ? 'yes' : 'no';
            }
        }

        return $data;
    }
}
