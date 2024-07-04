<?php

namespace App\Http\Resources\Career;

use App\Helpers\UtilityHelper;
use App\Helpers\WikipediaHelper;
use App\Http\Resources\Master\SkillResource;
use App\Services\JobTitleSkillServices;
use App\Services\UserJobTitlesService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $savedOn = null;
        $saved = 'no';
        $getPercentageOfSkills = JobTitleSkillServices::getPercentagesOfMatchedSkills($this->id);
        $createdAt = UserJobTitlesService::checkJobsExistsInUsers($this->id);
        if ($createdAt != false) {
            $savedOn = UtilityHelper::formatDateTime($createdAt->created_at);
            $saved = 'yes';
        }
        $response = [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'title'             => $this->title,
            'description'       => WikipediaHelper::fetchSkillDescription($this->title, $request->language),
            'skills'            => SkillResource::collection($this->skills),
            'lightcast_id'      => $this->lightcast_id,
            'related_challenges'=> $this->related_challenge == null ? 0 : $this->related_challenge->count(),
            'related_labs'      => $this->related_labs == null ? 0 : $this->related_labs->count(),
            'related_resources' => $this->related_resource == null ? 0 : $this->related_resource->count(),
            'saved_on'          => $savedOn,
            'saved'             => $saved,
            'skills_percentage' => intval($getPercentageOfSkills),
        ];
        if (auth()->user()) {
            if ($this->pinned && isset($this->pinned->pinned)) {
                $response['pinned'] = $this->pinned->pinned == 0 ? 'no' : 'yes';
            }
        }

        return $response;
    }
}
