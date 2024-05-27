<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserOrganizationListResource extends JsonResource
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
        $planName = 'NA';
        if ($this->chargebee_details) {
            switch ($this->chargebee_details->plan) {
                case 'free-plan-CAD-Yearly':
                    $planName = 'seed_plan_yearly';
                    break;
                case 'Sprout-Plan-CAD-Yearly':
                    $planName = 'sprout_plan_yearly';
                    break;
                case 'Budd-Plan-CAD-Yearly':
                    $planName = 'budd_plan_yearly';
                    break;
                case 'Bloom-Plan-CAD-Yearly':
                    $planName = 'bloom_plan_yearly';
                    break;
                case 'Unlimited-Plan-CAD-Yearly':
                    $planName = 'unlimited_plan';
                    break;
                default:
                    $planName = $this->chargebee_details->plan;
                    break;
            }
        }

        return [
            'id'                => $this->uuid,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'plan_name'         => $planName,
            'cover_image'       => $this->cover_image,
            'profile_image'     => $this->profile_image,
        ];
    }
}
