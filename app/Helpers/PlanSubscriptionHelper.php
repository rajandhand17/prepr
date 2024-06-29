<?php

namespace App\Helpers;

use App\Models\Challange;
use App\Models\Group;
use App\Models\Lab;
use App\Models\MemberManagement;
use App\Models\Organisation;
use App\Models\OrganizationInviteUser;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\User;
use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Customer;
use ChargeBee\ChargeBee\Models\Subscription;
use ChargeBee\ChargeBee\Models\SubscriptionEntitlement;
use Exception;

class PlanSubscriptionHelper
{
    // create customer everytime when new user register
    public static function createCustomer($user, $language)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $result = Customer::create([
                'firstName' => $user->first_name,
                'lastName'  => $user->last_name,
                'email'     => $user->email,
                'locale'    => $language,
            ]);
            $customer = $result->customer();
            $card = $result->card();

            return $customer;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    //assigning free plan to new user when he register or create new org
    public static function subscribePlan($customer, $plan_name, $org)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $result = Subscription::createWithItems($customer->id, [
                'subscriptionItems' => [[
                    'itemPriceId' => $plan_name,
                    'unitPrice'   => 0,
                    'quantity'    => 1,
                ],
                ],
                'cf_org_id'       => $org->id,
                'cf_organisation' => $org->name,
            ]);
            $subscription = $result->subscription();
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    // get customer id
    public static function getCustomer($userEmail)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $customer = [];
            $all = Customer::all([
                'email[is]' => $userEmail,
            ]);
            if ($all->count() != 0) {
                foreach ($all as $entry) {
                    $customer = $entry->customer();
                    $card = $entry->card();
                }
            }

            return $customer;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    //fetch subscription and their features limit based on org id
    public static function getSubscribedPlanDetailForOrg($org_id)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $all_Subscription = Subscription::all([
                'cf_org_id[is]' => $org_id,
            ]);
            if ($all_Subscription->count() > 0) {
                $subscription = $all_Subscription[0]->subscription();
                $subscriptionFeature = SubscriptionEntitlement::subscriptionEntitlementsForSubscription($subscription->id);
                $data = ['featureList' => $subscriptionFeature, 'subscriptionDetail' => $subscription];

                return $data;
            } else {
                return $data = [];
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    // get feature limits (created lab, challenge, group count)
    public static function getFeatureLimits($org_id)
    {
        try {
            $data = self::getSubscribedPlanDetailForOrg($org_id);
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
                }
            }

            return $Limits;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    // get Addons limits (created lab, challenge, group count)
    public static function getAddonsLimits($org_id)
    {
        try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
            $all_Subscription = Subscription::all([
                'cf_org_id[is]' => $org_id,
            ]);
            $addon = [];
            if ($all_Subscription->count() > 0) {
                $subscription = $all_Subscription[0]->subscription();
                foreach ($subscription->subscriptionItems as $item) {
                    if ($item->itemType === 'addon') {
                        if ($item->itemPriceId == 'challenge-creation-CAD-Monthly') {
                            $addon['challenge'] = $item->quantity;
                        } elseif ($item->itemPriceId == 'Resource-Creation-CAD-Monthly') {
                            $addon['resourceModule'] = $item->quantity;
                        } elseif ($item->itemPriceId == 'lab-creation-CAD-Monthly') {
                            $addon['lab'] = $item->quantity;
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

    public static function getTotalLimits($org_id, $component)
    {
        try {
            $featureLimit = self::getFeatureLimits($org_id);
            $addonLimit = self::getAddonsLimits($org_id);
            $totalLimit = [];
            if ($featureLimit || $addonLimit != []) {
                $totalLimit = (array_key_exists($component, $featureLimit) ? $featureLimit[$component] : 0) + (array_key_exists($component, $addonLimit) ? $addonLimit[$component] : 0);
            }

            return $totalLimit;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    // get created component ids (created lab, challenge, group count)
    public static function getComponentUsage($id, $component)
    {
        try {
            $organisation = Organisation::where('id', $id)->first();
            $componentUsage = [];
            if ($component == 'lab') {
                $componentUsage = Lab::where('organisation', $id)->where('is_auto_created', '0')->pluck('id')->sortBy('created_at');
            } elseif ($component == 'challenge') {
                $componentUsage = Challange::where(['organisation' => $id, 'is_auto_created' => '0'])->pluck('id')->sortBy('created_at');
            } elseif ($component == 'challengePath') {
                $componentUsage = Group::where('organisation', $id)->where('type', 'challenge')->pluck('id')->sortBy('created_at');
            } elseif ($component == 'labProgram') {
                $componentUsage = Group::where('organisation', $id)->where('type', 'lab')->pluck('id')->sortBy('created_at');
            } elseif ($component == 'resourceGroup') {
                $componentUsage = Group::where('organisation', $id)->where('type', 'resource')->pluck('id')->sortBy('created_at');
            } elseif ($component == 'resourceModule') {
                $componentUsage = Resource::where('org_id', $id)->where('is_auto_created', '0')->pluck('id')->sortBy('created_at');
            } elseif ($component == 'resourceCollection') {
                $componentUsage = ResourceGroup::where('org_id', $id)->pluck('id')->sortBy('created_at');
            } elseif ($component == 'managerInvite') {
                $componentUsage = OrganizationInviteUser::where('organisation_id', $id)->where('role', '!=', 'user')->count();
            } elseif ($component == 'userInvite') {
                $orgUserCount = $labsUserCount = $challengesUserCount = $challengesUserEmails = $challengesUser_Emails = $labsUserEmails = $labsUser_Emails = [];
                $orgUserCount = OrganizationInviteUser::where('organisation_id', $id)->where('role', 'user')->pluck('email')->toArray();
                $labs = Lab::select('id')->where('organisation', $id)->where('is_auto_created', '0')->pluck('id');
                $challange = Challange::select('id')->where(['organisation' => $id, 'is_auto_created' => '0'])->pluck('id');
                if ($labs->count() != 0) {
                    $labsUserEmails = MemberManagement::whereIn('module_id', $labs)->where('module_type', 'lab')->whereNull('invitee_id')->pluck('email')->toArray();
                }
                $labsUser_id = MemberManagement::whereIn('module_id', $labs)->where('module_type', 'lab')->whereNull('email')->pluck('invitee_id');
                $labsUser_Emails = User::select('email')->whereIn('id', $labsUser_id)->pluck('email')->toArray();
                $labsUserCount = array_merge($labsUserEmails, $labsUser_Emails);
                if ($challange->count() != 0) {
                    $challengesUserEmails = MemberManagement::whereIn('module_id', $challange)->where('module_type', 'challenge')->whereNull('invitee_id')->pluck('email')->toArray();
                }
                $challengesUser_id = MemberManagement::whereIn('module_id', $challange)->where('module_type', 'challenge')->whereNull('email')->pluck('invitee_id');
                $challengesUser_Emails = User::select('email')->whereIn('id', $challengesUser_id)->pluck('email')->toArray();
                $challengesUserCount = array_merge($challengesUserEmails, $challengesUser_Emails);
                $componentUsage = count(array_unique(array_merge($orgUserCount, $labsUserCount, $challengesUserCount)));
            }

            return $componentUsage;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getAccessedNonAccessedComponentIds($orgIds, $component)
    {
        try {
            if (is_array($orgIds) == false) {
                $orgIds = [$orgIds];
            }
            $componentIds = [];
            if (!empty($orgIds)) {
                foreach ($orgIds as $orgId) {
                    $totalLimit = self::getTotalLimits($orgId, $component);
                    $createdComponentIds = self::getComponentUsage($orgId, $component);
                    if ($createdComponentIds->count() != 0 && $totalLimit != []) {
                        foreach ($createdComponentIds as $key => $createdComponentId) {
                            if ($key < $totalLimit) {
                                $componentIds['accessed'][] = $createdComponentId;
                            } else {
                                $componentIds['nonAccessed'][] = $createdComponentId;
                            }
                        }
                    }
                }
            }

            return $componentIds;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
