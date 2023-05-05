<?php
namespace App\Helpers;

use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Subscription;
use ChargeBee\ChargeBee\Models\Customer;

class PlanSubscriptionHelper
{  
    public function __construct()
    {
        Environment::configure(env("Chargebee_Site"),env("Chargebee_Key"));
    }
    // create customer everytime when new user register 
    public static function createCustomer($user, $language){
        $result = Customer::create(array(
          "firstName" =>  $user->first_name,
          "lastName" => $user->last_name,
          "email" => $user->email,
          "locale" => $language,
          ));
        $customer = $result->customer();
        $card = $result->card();
        return $customer;
    }

    //assigning free plan to new user when he register
    public static function freePlanSubscribe($customer) {
    $result = Subscription::createWithItems($customer->id,array(
      "subscriptionItems" => array(array(
        "itemPriceId" => "free-plan-CAD-Yearly",
        "unitPrice" => 0,
        "quantity" => 1)
      )
      ));
      $subscription = $result->subscription();
      if($subscription){
          return $subscription;
      }
    }
}