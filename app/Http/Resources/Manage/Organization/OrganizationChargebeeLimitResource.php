<?php

namespace App\Http\Resources\Manage\Organization;

use App\Services\Manage\OrganizationService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationChargebeeLimitResource extends JsonResource
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
        if (!empty($this->chargebee_details)) {
            $organizationChargebeeLimit = OrganizationService::OrganizationChargebeeLimit($this);

            return $organizationChargebeeLimit;
        }
    }
}
