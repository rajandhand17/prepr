<?php

namespace App\Http\Resources\Manage\ResourceModule;

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
        if ($this->media == config('site-settings.aws_url') || $this->media == config('site-settings.aws_url').config('site-settings.default_resource_module_cover_image')) {
            $this->media = null;
        }

        return [
            'uuid'  => $this->uuid,
            'title' => $this->title,
            'media' => $this->media,
        ];
    }
}
