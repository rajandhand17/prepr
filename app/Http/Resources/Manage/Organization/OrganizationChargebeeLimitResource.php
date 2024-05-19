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
                    $planName = __('responses.seed_plan');
                    break;
                case 'Sprout-Plan-CAD-Yearly':
                    $planName = __('responses.sprout_plan');
                    break;
                case 'Budd-Plan-CAD-Yearly':
                    $planName = __('responses.budd_plan');
                    break;
                case 'Bloom-Plan-CAD-Yearly':
                    $planName = __('responses.bloom_plan');
                    break;
                case 'Unlimited-Plan-CAD-Yearly':
                    $planName = __('responses.enterprise_plan');
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
                'plan'                      => $planName,
                'labLimit'                  => $labLimit,
                'labProgramLimit'           => $labProgramLimit,
                'preBuildLab'               => $preBuildLab,
                'challengeLimit'            => $challengeLimit,
                'challengePathLimit'        => $challengePathLimit,
                'resourceModuleLimit'       => $resourceModuleLimit,
                'resourceCollectionLimit'   => $resourceCollectionLimit,
                'resourceGroupLimit'        => $resourceGroupLimit,
                'userInviteLimit'           => $userInviteLimit,
                'managerLimit'              => $managerLimit,
            ];
        }
    }
}
