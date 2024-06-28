<?php

namespace App\Http\Resources\Public\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationTypeModeResource extends JsonResource
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
        $value = null;
        switch ($this->value) {
            case '0':
                $value = 'assess';
                break;
            case '1':
                $value = 'onboard';
                break;
            case '2':
                $value = 'engage';
                break;
            case '3':
                $value = 'grow';
                break;
        }
        return [
            'type'          => $value,
        ];
    }
}
