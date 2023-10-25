<?php

namespace App\Http\Resources\Manage\Challenge;

use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'assessment_type'   => $this['assessment_type'],
            'visibility'        => $this['visibility'],
            'guidelines'        => $this['guidelines'],
            'attachments'       => $this['attachments'],
            'members'           => $this['members'],
        ];
    }
}
