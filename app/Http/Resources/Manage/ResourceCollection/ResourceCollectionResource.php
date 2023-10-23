<?php

namespace App\Http\Resources\Manage\ResourceCollection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $privacy = 'no';
        $status = 'draft';
        $level = 'Beginner Level';
        $duration = 'Less than 2 hours';
        $is_accessible = 'no';
        switch ($request->level) {
            case '1':
                $level="Beginner Level";
                break;
            case '2':
                $level="Intermediate Level";
                break;
            case '3':
                $level="Senior Level";
                break;
            case '4':
                $level="Advanced Level";
                break;
            case '5':
                $level="Junior Level";
                break;
            default:
                $level="Beginner Level";
        }
        switch ($request->duration) {
            case '1':
                $duration="Less than 2 hours";
                break;
            case '2':
                $duration="2 -4 hours";
                break;
            case '3':
                $duration="4 -8 hours";
                break;
            case '4':
                $duration="1 -2 Days";
                break;
            case '5':
                $duration="3 -5 Days";
                break;
            case '6':
                $duration="5+ Days";
                break;
            default:
                $duration="Less than 2 hours";
        }
        if (property_exists($this, 'privacy')) {
            switch ($this->privacy) {
                case "0":
                    $privacy = 'no';
                    break;
                case "1":
                    $privacy = 'yes';
                    break;
            }
        }

        if (property_exists($this, 'status')) {
            switch ($this->status) {
                case '0':
                    $status = 'draft';
                    break;
                case '1':
                    $status = 'published';
                    break;
                case '2':
                    $status = 'archive';
                    break;
            }
        }
        if (property_exists($this, 'is_accessible')) {
            switch ($this->is_accessible) {
                case '0':
                    $is_accessible = 'no';
                    break;
                case '1':
                    $is_accessible = 'yes';
                    break;
            }
        }
        return [
            'id'                                      => $this->uuid,
            'language'                                => $this->language,
            'title'                                   => $this->title,
            'organization_id'                         => $this->organization_id,
            'slug'                                    => $this->slug,
            'description'                             => $this->description,
            'media_type'                              => $this->media_type,
            'cover_image'                             => $this->media,
            'privacy'                                 => $privacy,
            'status'                                  => $status,
            'level'                                   => $level,
            'duration'                                => $duration,
            'is_accessible'                           => $is_accessible,
        ];
    }
}
