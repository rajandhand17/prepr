<?php

namespace App\Services\Manage;

use App\Models\ChargebeeSubscription;
use Exception;
use Illuminate\Support\Facades\Log;

class ChargebeeSubscriptionService
{
    public static function feedChargebeeDetails($organizationId, $chargebeeDetails)
    {
        try {
            $checkChargebeeDetail = ChargebeeSubscription::where('organizatin_id', $organizationId)->first();
            if ($checkChargebeeDetail) {
                $chargebeeDetail = $checkChargebeeDetail;
                $chargebeeDetail->delete();
            }

            // Setting values to chargebee_subscription table if values are set to unlimited then it would be -1 rest it can be zero or defined one's
            $managerInvite = ($chargebeeDetails['featureLimits']['managerInvite'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['managerInvite'] ?? 0);
            $userInvite = ($chargebeeDetails['featureLimits']['userInvite'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['userInvite'] ?? 0);
            $lab = ($chargebeeDetails['featureLimits']['lab'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['lab'] ?? 0);
            $labProgram = ($chargebeeDetails['featureLimits']['labProgram'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['labProgram'] ?? 0);
            $challenge = ($chargebeeDetails['featureLimits']['challenge'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['challenge'] ?? 0);
            $challengePath = ($chargebeeDetails['featureLimits']['challengePath'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['challengePath'] ?? 0);
            $resourceModule = ($chargebeeDetails['featureLimits']['resourceModule'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['resourceModule'] ?? 0);
            $resourceCollection = ($chargebeeDetails['featureLimits']['resourceCollection'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['resourceCollection'] ?? 0);
            $resourceGroup = ($chargebeeDetails['featureLimits']['resourceGroup'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['resourceGroup'] ?? 0);
            $preBuiltLab = ($chargebeeDetails['featureLimits']['preBuiltLab'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['preBuiltLab'] ?? 0);

            $chargebeeSubscription = new ChargebeeSubscription();
            $chargebeeSubscription->organizatin_id = $organizationId;
            $chargebeeSubscription->plan = $chargebeeDetails['subscriptionDetail']->subscriptionItems[0]->itemPriceId;
            $chargebeeSubscription->plan_validity = '0';
            $chargebeeSubscription->plan_limitations = ($chargebeeDetails['subscriptionDetail']->subscriptionItems[0]->itemPriceId === 'Unlimited-Plan-CAD-Yearly') ? '1' : '0';
            $chargebeeSubscription->challenge_limits = $challenge;
            $chargebeeSubscription->challenge_path_limits = $challengePath;
            $chargebeeSubscription->lab_limits = $lab;
            $chargebeeSubscription->lab_program_limits = $labProgram;
            $chargebeeSubscription->pre_build_lab_limits = $preBuiltLab;
            $chargebeeSubscription->resource_module_limits = $resourceModule;
            $chargebeeSubscription->resource_collection_limits = $resourceCollection;
            $chargebeeSubscription->resource_group_limits = $resourceGroup;
            $chargebeeSubscription->user_invite_limits = $userInvite;
            $chargebeeSubscription->organization_invite_limits = $managerInvite;
            $chargebeeSubscription->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
