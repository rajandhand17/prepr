<?php

namespace App\Http\Resources\Explore;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'media'=>$this->media,
        ];
    }
}
