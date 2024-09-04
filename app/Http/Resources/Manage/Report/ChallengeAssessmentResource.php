<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title ?? '-',
            'description' => $this->description ?? '-',
            'media'       => $this->media ?? '-',
            'created_by'  => data_get($this->createdBy,'full_name'),
            'achievement' => 'Participation Achievement',
        ];
    }
}
