<?php

namespace App\Http\Resources\Manage\ResourceModule;

use App\Helpers\UtilityHelper;
use App\Http\Resources\Manage\Organization\OrganizationAddressResource;
use App\Http\Resources\Manage\Organization\OrganizationMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                           => $this->uuid,
            'language'                     => $this->language,
            'title'                        => $this->title,
            'slug'                         => $this->slug,
            'description'                  => $this->description,
            'status'                       => ($this->status == '0') ? 'open' : 'close',
            'is_global'                    => ($this->is_global=='0') ? 'no' : 'yes',
        ];
    }
}
