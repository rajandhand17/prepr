<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectAdditionalInfoResource extends JsonResource
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
        $category = null;
        $category_id = null;
        $industry = null;
        $industry_id = null;
        $verticals = null;
        $verticals_id = null;
        $type = null;
        $type_id = null;
        $stage = null;
        $stage_id = null;
        $status = null;
        $status_id = null;

        if ($this->category_id) {
            $category = $this->getCategory->title;
            $category_id = $this->getCategory->id;
        }

        if ($this->industry_id) {
            $industry = $this->getIndustry->title;
            $industry_id = $this->getIndustry->id;
        }

        if ($this->verticals_id) {
            $verticals = $this->getVerticals->title;
            $verticals_id = $this->getVerticals->id;
        }

        if ($this->type_id) {
            $type = $this->getType->title;
            $type_id = $this->gettype->id;
        }

        if ($this->stage_id) {
            $stage = $this->getStage->title;
            $stage_id = $this->getStage->id;
        }

        if ($this->status_id) {
            $status = $this->getStatus->title;
            $status_id = $this->getStatus->id;
        }

        return [
            'category'              => $category,
            'category_id'           => $category_id,
            'industry'              => $industry,
            'industry_id'           => $industry_id,
            'verticals'             => $verticals,
            'verticals_id'          => $verticals_id,
            'type'                  => $type,
            'type_id'               => $type_id,
            'stage'                 => $stage,
            'stage_id'              => $stage_id,
            'status'                => $status,
            'status_id'             => $status_id,
        ];
    }
}
