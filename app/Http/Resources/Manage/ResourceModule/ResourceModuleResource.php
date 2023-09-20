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
        return parent::toArray($request);

    }
}
