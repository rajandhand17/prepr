<?php

namespace App\Http\Resources\Manage\Organization;

use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationMemberResource extends JsonResource
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

        $membersDetails=UserService::getUserByEmail($this->email);
        return [
            'id'            => $membersDetails->id,
            'name'          => $membersDetails->full_name,
            'description'   => $membersDetails->description,
            'position'      => $membersDetails->user_rank,
            'image'         => $membersDetails->profile_image,
        ];
    }
}
