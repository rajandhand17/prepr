<?php

namespace App\Http\Resources\Manage\LabMarketplace;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabMarketplaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'uuid'            => $this->uuid,
            'language'        => $this->language,
            'user_id'         => $this->user_id,
            'organization_id' => $this->organization_id,
            'slug'            => $this->slug,
            'title'           => $this->title,
            'description'     => $this->description,
        ];
    }
}
