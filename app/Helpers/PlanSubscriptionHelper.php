<?php
namespace App\Helpers;

use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Subscription;
use ChargeBee\ChargeBee\Models\Customer;
use ChargeBee\ChargeBee\Models\Feature;
use ChargeBee\ChargeBee\Models\SubscriptionEntitlement;
class PlanSubscriptionHelper
{  
   
    // create customer everytime when new user register 
    public static function createCustomer($user, $language){
      Environment::configure(config('chargebee.Chargebee_Site'), config('chargebee.Chargebee_Key'));
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
      Environment::configure(config('chargebee.Chargebee_Site'), config('chargebee.Chargebee_Key'));
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

    //fetech particular subscription plan
    public static function fetechSubscriptionPlan($request)
    {  
      Environment::configure(config('chargebee.Chargebee_Site'), config('chargebee.Chargebee_Key'));
        $all = Subscription::all(array(
          "cf_org_id[is]" =>$request->org_id
              ));
            
        return $all;
    }

    // get customer id
    public static function getCustomer($userEmail) {
      try {
        Environment::configure(config('chargebee.Chargebee_Site'), config('chargebee.Chargebee_Key'));
        $customer = [];
        $all = Customer::all(array(
        "email[is]" => $userEmail,
        ));
        if( $all->count() != 0) {
          foreach($all as $entry){
            $customer = $entry->customer();
            $card = $entry->card();
          }
        }
        return $customer;
      } catch(\Exception $e) {
        return false;
      }
    }

    public static function getFeaturesList($request)
    {
      try {
        Environment::configure(config('chargebee.Chargebee_Site'), config('chargebee.Chargebee_Key'));
                     $all = Subscription::all(array(
                        "cf_org_id[is]" =>$request->org_id
                            ));
               if($all->count() > 0){
               foreach($all as $entry){
                   $subscription = $entry->subscription();
                   $customer = $entry->customer();
                   $card = $entry->card();
                   $alll = SubscriptionEntitlement::subscriptionEntitlementsForSubscription( $subscription->id);
               }
               $data = ['featureList' => $alll, 'subscriptionDetail' => $subscription];
               return $data;
           }
      }catch(\Exception $e){
        return $e;
        return false;
      }
    }

}