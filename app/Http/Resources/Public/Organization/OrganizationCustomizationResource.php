<?php

namespace App\Http\Resources\Public\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationCustomizationResource extends JsonResource
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
        switch ($this->enable_custom_login_and_registration) {
            case '0':
                $enable_custom_login_and_registration = 'no';
                break;
            case '1':
                $enable_custom_login_and_registration = 'yes';
                break;
            default:
                $enable_custom_login_and_registration = 'no';
                break;
        }

        switch ($this->use_main_org_logo) {
            case '0':
                $use_main_org_logo = 'no';
                break;
            case '1':
                $use_main_org_logo = 'yes';
                break;
            default:
                $use_main_org_logo = 'no';
                break;
        }
        $custom_logo_image = ($this->getRawOriginal('custom_logo_image') != null) ? $this->custom_logo_image : null;
        $custom_hero_image = ($this->getRawOriginal('custom_hero_image') != null) ? $this->custom_hero_image : null;

        return [
            'enable_custom_login_and_registration'          => $enable_custom_login_and_registration,
            'use_main_org_logo'                             => $use_main_org_logo,
            'custom_logo_image'                             => $custom_logo_image,
            'custom_hero_image'                             => $custom_hero_image,
            'custom_background_color'                       => $this->custom_background_color,
        ];
    }
}
