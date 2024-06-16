<?php

namespace App\Http\Resources\Public\Organization;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
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
            $getUserCount = ChargebeeHelper::getUserCount($this->id);
            $getManagerCount = ChargebeeHelper::getManagerCount($this->id);
            switch ($this->chargebee_details->plan) {
                case 'free-plan-CAD-Yearly':
                    $plan = 'seed_plan_yearly';
                    $planName = __('responses.seed_plan');
                    break;
                case 'Sprout-Plan-CAD-Yearly':
                    $plan = 'sprout_plan_yearly';
                    $planName = __('responses.sprout_plan');
                    break;
                case 'Budd-Plan-CAD-Yearly':
                    $plan = 'budd_plan_yearly';
                    $planName = __('responses.budd_plan');
                    break;
                case 'Bloom-Plan-CAD-Yearly':
                    $plan = 'bloom_plan_yearly';
                    $planName = __('responses.bloom_plan');
                    break;
                case 'Unlimited-Plan-CAD-Yearly':
                    $plan = 'unlimited_plan';
                    $planName = __('responses.enterprise_plan');
                    break;
            }

            switch ($this->chargebee_details->plan) {
                case 'free-plan-CAD-Monthly':
                    $plan = 'seed_plan_monthly';
                    $planName = __('responses.seed_plan');
                    break;
                case 'Sprout-Plan-CAD-Monthly':
                    $plan = 'sprout_plan_monthly';
                    $planName = __('responses.sprout_plan');
                    break;
                case 'Budd-Plan-CAD-Monthly':
                    $plan = 'budd_plan_monthly';
                    $planName = __('responses.budd_plan');
                    break;
                case 'Bloom-Plan-CAD-Monthly':
                    $plan = 'bloom_plan_monthly';
                    $planName = __('responses.bloom_plan');
                    break;
                case 'Unlimited-Plan-CAD-Monthly':
                    $plan = 'unlimited_plan';
                    $planName = __('responses.enterprise_plan');
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
                'plan'                          => $plan,
                'plan_name'                     => $planName,
                'plan_end_date'                 => UtilityHelper::formatDateTime($this->chargebee_details->trial_end_date),
                'lab_limit'                     => $labLimit,
                'lab_count'                     => $this->labs_count->count(),
                'lab_program_limit'             => $labProgramLimit,
                'lab_program_count'             => $this->lab_programs_count->count(),
                'pre_build_lab'                 => $preBuildLab,
                'challenge_limit'               => $challengeLimit,
                'challenge_count'               => $this->challenges_count->count(),
                'challenge_path_limit'          => $challengePathLimit,
                'challenge_path_count'          => $this->challenge_paths_count->count(),
                'resource_module_limit'         => $resourceModuleLimit,
                'resource_module_count'         => $this->resource_modules_count->count(),
                'resource_collection_limit'     => $resourceCollectionLimit,
                'resource_collection_count'     => $this->resource_collections_count->count(),
                'resource_group_limit'          => $resourceGroupLimit,
                'resource_group_count'          => $this->resource_groups_count->count(),
                'user_invite_limit'             => $userInviteLimit,
                'user_invite_count'             => $getUserCount->count(),
                'manager_limit'                 => $managerLimit,
                'manager_count'                 => $getManagerCount->count(),
            ];
        }
    }
}
