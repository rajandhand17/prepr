<?php

namespace App\Helpers;

use App\Services\Manage\ChargebeeSubscriptionService;
use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Customer;
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
            return false;
        }
    }

    //assigning free plan to new user when he register or create new org
    public static function subscribePlan($user, $organization, $planDetail)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $subscribePlan = Subscription::createWithItems($user->id, [
                'subscriptionItems' => [[
                    'itemPriceId' => $planDetail,
                ],
                ],
                'cf_org_id'       => $organization->id,
                'cf_organisation' => $organization->title,
            ]);
            $subscription = $subscribePlan->subscription();

            self::createChargebeePlanDetails($organization->id);
        } catch(Exception $e) {
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
            return false;
        }
    }

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
            return false;
        }
    }
}
