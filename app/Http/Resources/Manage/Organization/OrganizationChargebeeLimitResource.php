<?php

namespace App\Http\Resources\Manage\Organization;

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

            if ($this->chargebee_details->plan === 'Unlimited-Plan-CAD-Yearly') {
                $labLimit = 'UnLimited';
                $labProgramLimit = 'UnLimited';
                $preBuildLab = 'UnLimited';
                $challengeLimit = 'UnLimited';
                $challengePathLimit = 'UnLimited';
                $resourceModuleLimit = 'UnLimited';
                $resourceCollectionLimit = 'UnLimited';
                $resourceGroupLimit = 'UnLimited';
                $userInviteLimit = 'UnLimited';
                $managerLimit = 'UnLimited';
            } else {
                $labLimit = $this->chargebee_details->lab_limits;
                $labProgramLimit = $this->chargebee_details->lab_program_limits;
                $preBuildLab = $this->chargebee_details->pre_build_lab_limits;
                $challengeLimit = $this->chargebee_details->challenge_limits;
                $challengePathLimit = $this->chargebee_details->challenge_path_limits;
                $resourceModuleLimit = $this->chargebee_details->resource_module_limits;
                $resourceCollectionLimit = $this->chargebee_details->resource_collection_limits;
                $resourceGroupLimit = $this->chargebee_details->resource_group_limits;
                $userInviteLimit = $this->chargebee_details->user_invite_limits;
                $managerLimit = $this->chargebee_details->organization_invite_limits;
            }

            return [
                'plan'                        => $planName,
                'lab_limit'                   => $labLimit,
                'lab_count'                   => $this->labs_count->count(),
                'lab_program_limit'           => $labProgramLimit,
                'lab_program_count'           => $this->lab_programs_count->count(),
                'pre_build_lab'               => $preBuildLab,
                'challenge_limit'             => $challengeLimit,
                'challenge_count'             => $this->challenges_count->count(),
                'challenge_path_limit'        => $challengePathLimit,
                'challenge_path_count'        => $this->challenge_paths_count->count(),
                'resource_module_limit'       => $resourceModuleLimit,
                'resource_module_count'       => $this->resource_modules_count->count(),
                'resource_collection_limit'   => $resourceCollectionLimit,
                'resource_collection_count'   => $this->resource_collections_count->count(),
                'resource_group_limit'        => $resourceGroupLimit,
                'resource_group_count'        => $this->resource_groups_count->count(),
                'user_invite_limit'           => $userInviteLimit,
                'manager_limit'               => $managerLimit,
            ];
        }
    }
}
