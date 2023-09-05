<?php

namespace App\Http\Resources\Manage\LabProgram;

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
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'is_auto_created'               => $this->is_auto_created,
        ];
    }
}
