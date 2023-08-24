<?php

namespace App\Http\Resources\Public\Lab;

use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
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
        $category = $this->getCategory;
        if ($category) {
            $category = $this->getCategory->id;
        } else {
            $category = null;
        }

        $joined_status = $this->joined();
        $join_status = 'No';
        if ($joined_status) {
            switch ($joined_status->invite_status) {
                case '0':
                    $join_status = 'Invited';
                    break;
                case '1':
                    $join_status = 'Yes';
                    break;
                case '2':
                    $join_status = 'Pending';
                    break;
                case '3':
                    $join_status = 'No';
                    break;
                default:
                    $join_status = 'No';
                    break;
            }
        }

        return [
            'id'                           => $this->uuid,
            'language'                     => $this->language,
            'title'                        => $this->title,
            'slug'                         => $this->slug,
            'description'                  => $this->description,
            'privacy'                      => $this->type,
            'media_type'                   => $this->media_type,
            'media'                        => $this->media,
            'category'                     => $category,
            'status'                       => $this->status,
            'member_count'                 => $this->members()->count(),
            'likes'                        => $this->likes()->count(),
            'shares'                       => $this->shares()->count(),
            'joined'                       => $join_status,
            'liked'                        => $this->liked(),
            'favourite'                    => $this->favourite(),
            'lab_address'                  => LabAddressResource::make($this->address),
            'lab_achievement'              => LabAchievementResource::make($this->achievement),
            'lab_external_links'           => LabExternalLinksResource::collection($this->external_links),
        ];
    }
}
