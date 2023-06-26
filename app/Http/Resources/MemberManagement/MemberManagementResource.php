<?php

namespace App\Http\Resources\MemberManagement;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Organization\OrganizationAddressResource;
class MemberManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {     
      $member_management = [
        'invitee_name'       =>$this->invitee_name,
        'invite_status'       => $this->invite_status,
        'email'               => $this->email,
        'organization'        => OrganizationMembermangementResource::collection($this->organizations),
        'organization_address'=> OrganizationAddressResource::collection($this->organizationAddress),
    ];
    return $member_management;
    }
}
