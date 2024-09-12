<?php

namespace App\Http\Resources\Manage\Scorm;

use App\Models\Scorm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Scorm
 */
class ScormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'        => $this->uuid,
            'title'       => $this->title,
            'version'     => $this->version,
            'origin_file' => $this->origin_file,
            'completed'   => $this->whenHas('completed'),
        ];
    }
}
