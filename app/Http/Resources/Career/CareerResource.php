<?php

namespace App\Http\Resources\Career;

use App\Helpers\UtilityHelper;
use App\Helpers\WikipediaHelper;
use App\Http\Resources\Master\SkillResource;
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
            'saved_on'          => $this->created_at == null ? '' : UtilityHelper::formatDateTime($this->created_at),
            'saved'             => $this->saved_jobs(),
        ];
        if($this->pinned && isset($this->pinned->pinned)){
            $response['pinned'] =$this->pinned->pinned == 0 ? 'no' : 'yes';
        }

        return $response;
    }
}
