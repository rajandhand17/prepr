<?php

namespace App\Helpers;

use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChargebeeSubscription;
use App\Models\Lab;
use App\Models\LabProgram;
use App\Models\MemberManagement;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChargebeeSubscriptionService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\UserService;
use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Customer;
use ChargeBee\ChargeBee\Models\Entitlement;
use ChargeBee\ChargeBee\Models\Item;
use ChargeBee\ChargeBee\Models\Plan;
use ChargeBee\ChargeBee\Models\Subscription;
use ChargeBee\ChargeBee\Models\SubscriptionEntitlement;
use Exception;

class ChargebeeHelper
{
    // create customer everytime when new organization user register
    public static function createCustomer($user)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $createCustomer = Customer::create([
                'firstName' => $user->first_name,
                'lastName'  => $user->last_name,
                'email'     => $user->email,
                'locale'    => $user->preferred_language,
            ]);
            $customer = $createCustomer->customer();
            $card = $createCustomer->card();

            return $customer;
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    //assigning free plan to new user when he register or create new org
    public static function subscribePlan($customerDetails, $organization, $planDetail)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $subscribePlan = Subscription::createWithItems($customerDetails->id, [
                'subscriptionItems' => [[
                    'itemPriceId' => $planDetail,
                ],
                ],
                'cf_org_id'       => $organization->id,
                'cf_organisation' => $organization->title,
            ]);
            $subscription = $subscribePlan->subscription();
        } catch(Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // get customer detail from chargebee by passing customer email
    public static function getCustomer($userEmail)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $getCustomer = [];
            $all = Customer::all([
                'email[is]' => $userEmail,
            ]);
            if ($all->count() != 0) {
                foreach ($all as $entry) {
                    $getCustomer = $entry->customer();
                    $card = $entry->card();
                }
            }

            return $getCustomer;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // get organization plan details using organization id and also this function would be called and update in the learnlab db
    public static function getSubscribedPlanDetailForOrganization($organizationId)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $fetchAllSubscriptions = Subscription::all([
                'cf_org_id[is]' => $organizationId,
            ]);
            if ($fetchAllSubscriptions->count() > 0) {
                $subscription = $fetchAllSubscriptions[0]->subscription();
                $subscriptionFeature = SubscriptionEntitlement::subscriptionEntitlementsForSubscription($subscription->id);
                $data = ['subscriptionDetail' => $subscription, 'featureList' => $subscriptionFeature];

                return $data;
            } else {
                return $data = [];
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // get features limits like (created lab, challenge, group count etc)
    public static function getFeatureLimits($organizationId)
    {
        try {
            $data = self::getSubscribedPlanDetailForOrganization($organizationId);
            $Limits = [];
            if ($data != []) {
                foreach ($data['featureList'] as $feature) {
                    $subscriptionEntitlement = $feature->subscriptionEntitlement();
                    if ($subscriptionEntitlement->featureId == 'resource-creation') {
                        $Limits['resourceModule'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'challenge-creation') {
                        $Limits['challenge'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'lab-creation') {
                        $Limits['lab'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'resource-collection-creation') {
                        $Limits['resourceCollection'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'resource-group-creation') {
                        $Limits['resourceGroup'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'challenge-path-creation') {
                        $Limits['challengePath'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'lab-program-creation') {
                        $Limits['labProgram'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'organisation-manager-invite') {
                        $Limits['managerInvite'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'user-invite') {
                        $Limits['userInvite'] = $subscriptionEntitlement->value;
                    }
                    if ($subscriptionEntitlement->featureId == 'pre-built-labs') {
                        $Limits['preBuiltLab'] = $subscriptionEntitlement->value;
                    }
                }
            }

            return $Limits;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // get Addons limits (created lab, challenge, group count)
    public static function getAddonsLimits($organizationId)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $allSubscriptions = Subscription::all([
                'cf_org_id[is]' => $organizationId,
            ]);
            $addon = [];
            if ($allSubscriptions->count() > 0) {
                $subscription = $allSubscriptions[0]->subscription();
                foreach ($subscription->subscriptionItems as $item) {
                    if ($item->itemType === 'addon') {
                        if ($item->itemPriceId == config('chargebee.chargebee_addon.challenge_addon_yearly')) {
                            $addon['challenge'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.resource_module_addon_yearly')) {
                            $addon['resourceModule'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.lab_addon_yearly')) {
                            $addon['lab'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.user_addon_yearly')) {
                            $addon['userInvite'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.manager_addon_yearly')) {
                            $addon['managerInvite'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.challenge_path_addon_yearly')) {
                            $addon['challengePath'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.lab_program_addon_yearly')) {
                            $addon['labProgram'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.resource_group_addon_yearly')) {
                            $addon['resourceGroup'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.resource_collection_addon_yearly')) {
                            $addon['resourceCollection'] = $item->quantity;
                        } elseif ($item->itemPriceId == config('chargebee.chargebee_addon.paid_lab_addon_yearly')) {
                            $addon['preBuiltLab'] = $item->quantity;
                        }
                    }
                }
            }

            return $addon;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // get plan details and their limits from the API.
    public static function getPlanDetailsAndLimits($organizationId)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $fetchAllSubscriptions = Subscription::all([
                'cf_org_id[is]' => $organizationId,
            ]);
            if ($fetchAllSubscriptions->count() > 0) {
                $subscription = $fetchAllSubscriptions[0]->subscription();
                $subscriptionFeatureLimits = self::getFeatureLimits($organizationId);
                $data = ['subscriptionDetail' => $subscription, 'featureLimits' => $subscriptionFeatureLimits];

                return $data;
            } else {
                return $data = [];
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // get plan details and their limits from the API.
    public static function getAllPlanDetailsAndLimits()
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            // Fetch all plans
            $allPlans = Item::all([
                'type[is]' => 'plan',
            ]);
            // Prepare an array to hold features and their associated plan limits
            $featuresLimits = [];
            // Loop through all plans
            foreach ($allPlans as $entry) {
                $item = $entry->item();
                $planId = $item->id;
                // Fetch entitlements for the plan
                $entitlements = Entitlement::all([
                    'entity_id[is]' => $planId,
                ]);

                foreach ($entitlements as $entitlementEntry) {
                    $entitlement = $entitlementEntry->entitlement();
                    $featureId = $entitlement->featureId;
                    // Check if this feature is already in the featuresLimits array
                    if (!isset($featuresLimits[$featureId])) {
                        $featuresLimits[$featureId] = [
                            'feature_id' => $featureId,
                            'limits'     => [],
                        ];
                    }
                    // Add the limit for this plan under the corresponding feature
                    $featuresLimits[$featureId]['limits'][$planId] = $entitlement->value;
                }
            }

            // Convert associative featureLimits to an indexed array
            $featuresLimits = array_values($featuresLimits);

            return $featuresLimits;
        } catch (Exception $e) {
            // Log the error and return false
            UtilityHelper::logError($e);

            return false;
        }
    }

    // below function is used to create the entries in the local db when new organization plans are registered
    public static function createChargebeePlanDetails($organizationId)
    {
        try {
            $fetchChargebeeDetails = self::getPlanDetailsAndLimits($organizationId);
            if ($fetchChargebeeDetails != []) {
                $feedChargebeeDetails = ChargebeeSubscriptionService::feedChargebeeDetails($organizationId, $fetchChargebeeDetails);
                if (!$feedChargebeeDetails) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getTotalLimits($organizationId, $component)
    {
        try {
            $featuresLimit = self::getFeatureLimits($organizationId);
            $addonsLimit = self::getAddonsLimits($organizationId);
            $totalLimit = [];
            if ($featuresLimit || $addonsLimit != []) {
                $featuresLimit[$component] = array_key_exists($component, $featuresLimit) ? $featuresLimit[$component] : '0';
                if ($featuresLimit[$component] != 'Unlimited') {
                    $totalLimit = $featuresLimit[$component] + (array_key_exists($component, $addonsLimit) ? $addonsLimit[$component] : 0);
                } else {
                    $totalLimit = 'Unlimited';
                }
            }

            return $totalLimit;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getComponentUsage($organizationId, $component)
    {
        try {
            $componentUsage = [];
            if ($component === 'labs') {
                $componentUsage = Lab::where(['organization_id' => $organizationId, 'is_pre_built' => '0', 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'preBuiltLabs') {
                $componentUsage = Lab::where(['organization_id' => $organizationId, 'is_pre_built' => '1', 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'labPrograms') {
                $componentUsage = LabProgram::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'challenges') {
                $componentUsage = Challenge::where(['organization_id' => $organizationId, 'is_pre_built' => '0', 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'challengePaths') {
                $componentUsage = ChallengePath::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'resourceModules') {
                $componentUsage = ResourceModule::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'resourceCollections') {
                $componentUsage = ResourceCollection::where(['organization_id' => $organizationId])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'resourceGroups') {
                $componentUsage = ResourceGroup::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component === 'managerInvites') {
                $componentUsage = MemberManagement::where(['module_id' => $organizationId, 'module_type' => '0'])->where('role', '!=', 'User')->whereNull('deleted_at')->count();
            } elseif ($component === 'userInvites') {
                $componentUsage = MemberManagement::where('module_type', '0')
                    ->where(function ($query) use ($organizationId) {
                        $query->where('module_id', $organizationId)
                            ->where('role', 'User')
                            ->whereNull('deleted_at');
                    })
                    ->orWhereIn('module_id', function ($query) use ($organizationId) {
                        $query->select('id')
                            ->from('labs')
                            ->where('organization_id', $organizationId)
                            ->where('is_auto_created', '0');
                    })
                    ->orWhereIn('module_id', function ($query) use ($organizationId) {
                        $query->select('id')
                            ->from('challenges')
                            ->where('organization_id', $organizationId)
                            ->where('is_auto_created', '0');
                    })
                    ->whereIn('module_type', ['1', '2'])
                    ->count();
            }

            return $componentUsage;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkComponentLimitBasedOnOrganization($organizationID, $component)
    {
        try {
            $fetchOrganizationPlan = ChargebeeSubscription::where('organization_id', $organizationID)->first();
            if ($fetchOrganizationPlan) {
                switch ($component) {
                    case 'lab':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->lab_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->lab_limits;
                        break;
                    case 'labProgram':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->lab_program_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->lab_program_limits;
                        break;
                    case 'challenge':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->challenge_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->challenge_limits;
                        break;
                    case 'challengePath':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->challenge_path_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->challenge_path_limits;
                        break;
                    case 'resourceModule':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->resource_module_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->resource_module_limits;
                        break;
                    case 'resourceCollection':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->resource_collection_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->resource_collection_limits;
                        break;
                    case 'resourceGroup':
                        $fetchOrganizationPlanDetails = ($fetchOrganizationPlan->resource_group_limits === -1) ? 'Unlimited' : $fetchOrganizationPlan->resource_group_limits;
                        break;
                    default:
                        $fetchOrganizationPlanDetails = 0;
                        break;
                }
            } else {
                $fetchOrganizationPlanDetails = self::getTotalLimits($organizationID, $component);
            }

            $data = ['organizationId' => $organizationID, 'fetchOrganizationPlanDetails' => $fetchOrganizationPlanDetails];

            return $data;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserCount($organizationId)
    {
        try {
            $labUserInviteCount = LabService::labUserInviteCount($organizationId);
            $challengeUserInviteCount = ChallengeService::challengeUserInviteCount($organizationId);
            $organizationUserInviteCount = OrganizationService::organizationUserInviteCount($organizationId);
            $mergedMemberEmails = $labUserInviteCount->merge($challengeUserInviteCount)->merge($organizationUserInviteCount);
            $userInviteCount = UserService::getUserByEmailArray($mergedMemberEmails->unique());

            return $userInviteCount;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getManagerCount($organizationId)
    {
        try {
            $organizationManagerInviteCount = OrganizationService::organizationManagerInviteCount($organizationId);
            $managerInviteCount = UserService::getUserByEmailArray($organizationManagerInviteCount);

            return $managerInviteCount;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteOrganizationSubscription($organizationID)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $fetchAllSubscriptions = Subscription::all([
                'cf_org_id[is]' => $organizationID,
            ]);
            if ($fetchAllSubscriptions->count() > 0) {
                $subscriptionData = $fetchAllSubscriptions[0]->subscription();
                $result = Subscription::delete($subscriptionData->id);
                $subscription = $result->subscription();
                $customer = $result->customer();
                $card = $result->card();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
