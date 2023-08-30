<?php

namespace App\Http\Resources\Manage\LabProgram;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                            => $this->id,
            'language'                      => $this->language,
            'title'                         => $this->title,
            'slug'                          => $this->slug,
            'description'                   => $this->description,
            'lab_id'                        => $this->lab_id,
            'user_id'                       => $this->user_id,
            'media'                         => $this->media,
            'privacy'                       => $this->privacy,
            'status'                        => $this->status,
            'is_auto_created'               => $this->is_auto_created,
            'prize'                         => $this->prize,
            'points'                        => $this->points,
            'trophy'                        => $this->trophy,

        ];
    }
}
