<?php

namespace App\Http\Resources\Public\ResourceModule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceModuleListNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'uuid'  => $this->uuid,
            'title' => $this->title,
            'media' => $this->media,
        ];
    }
}
