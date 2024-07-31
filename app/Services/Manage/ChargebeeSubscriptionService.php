<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChargebeeSubscription;
use Carbon\Carbon;
use Exception;

class ChargebeeSubscriptionService
{
    public static function feedChargebeeDetails($organizationId, $chargebeeDetails)
    {
        try {
            $checkChargebeeDetail = ChargebeeSubscription::where('organization_id', $organizationId)->orderby('id', 'ASC')->first();
            if ($checkChargebeeDetail && $checkChargebeeDetail->plan === $chargebeeDetails['subscriptionDetail']->subscriptionItems[0]->itemPriceId) {
                return true; //marking true if no change in plan subscription
            }
            if ($checkChargebeeDetail) {
                $chargebeeOldDetailDelete = ChargebeeSubscription::where('organization_id', $organizationId)->delete();
            }

            // Setting values to chargebee_subscription table if values are set to unlimited then it would be -1 rest it can be zero or defined one's
            $managerInvite = (isset($chargebeeDetails['featureLimits']['managerInvite']) && $chargebeeDetails['featureLimits']['managerInvite'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['managerInvite'] ?? 0);
            $userInvite = (isset($chargebeeDetails['featureLimits']['userInvite']) && $chargebeeDetails['featureLimits']['userInvite'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['userInvite'] ?? 0);
            $lab = (isset($chargebeeDetails['featureLimits']['lab']) && $chargebeeDetails['featureLimits']['lab'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['lab'] ?? 0);
            $labProgram = (isset($chargebeeDetails['featureLimits']['labProgram']) && $chargebeeDetails['featureLimits']['labProgram'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['labProgram'] ?? 0);
            $challenge = (isset($chargebeeDetails['featureLimits']['challenge']) && $chargebeeDetails['featureLimits']['challenge'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['challenge'] ?? 0);
            $challengePath = (isset($chargebeeDetails['featureLimits']['challengePath']) && $chargebeeDetails['featureLimits']['challengePath'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['challengePath'] ?? 0);
            $resourceModule = (isset($chargebeeDetails['featureLimits']['resourceModule']) && $chargebeeDetails['featureLimits']['resourceModule'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['resourceModule'] ?? 0);
            $resourceCollection = (isset($chargebeeDetails['featureLimits']['resourceCollection']) && $chargebeeDetails['featureLimits']['resourceCollection'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['resourceCollection'] ?? 0);
            $resourceGroup = (isset($chargebeeDetails['featureLimits']['resourceGroup']) && $chargebeeDetails['featureLimits']['resourceGroup'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['resourceGroup'] ?? 0);
            $preBuiltLab = (isset($chargebeeDetails['featureLimits']['preBuiltLab']) && $chargebeeDetails['featureLimits']['preBuiltLab'] === 'Unlimited') ? -1 : (int) ($chargebeeDetails['featureLimits']['preBuiltLab'] ?? 0);

            // Create a Carbon instance from the timestamp
            $date = Carbon::createFromTimestamp($chargebeeDetails['subscriptionDetail']->nextBillingAt);
            $trial_end_date = $date->format('Y-m-d H:i:s');

            $chargebeeSubscription = new ChargebeeSubscription();
            $chargebeeSubscription->organization_id = $organizationId;
            $chargebeeSubscription->plan = $chargebeeDetails['subscriptionDetail']->subscriptionItems[0]->itemPriceId;
            $chargebeeSubscription->plan_validity = '0';
            $chargebeeSubscription->plan_limitations = ($chargebeeDetails['subscriptionDetail']->subscriptionItems[0]->itemPriceId === 'Unlimited-Plan-CAD-Yearly') ? '1' : '0';
            $chargebeeSubscription->trial_end_date = $trial_end_date ?? null;
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChargebeeBasedOnSubscription($plan)
    {
        try {
            return ChargebeeSubscription::where('plan',config('chargebee.chargebee_plan.'.$plan))->pluck('organization_id');
        }catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
