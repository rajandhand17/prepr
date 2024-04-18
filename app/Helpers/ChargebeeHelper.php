<?php

namespace App\Helpers;

use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Customer;
use ChargeBee\ChargeBee\Models\Subscription;
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
                    'unitPrice'   => 0,
                    'quantity'    => 1,
                ],
                ],
                'cf_org_id'       => $organization->id,
                'cf_organisation' => $organization->name,
            ]);
            $subscription = $subscribePlan->subscription();
        } catch(Exception $e) {
            return false;
        }
    }
}
