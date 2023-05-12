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
        "itemPriceId" => config('chargebeeplans.Started_Plan'),
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

    public static function getSubscribedPlanForOrg($request)
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

     // get feature limits (created lab, challenge, group count)
  public static function getFeatureLimits($org_id)
  {
   try {
    $data = self::getSubscribedPlanDetailForOrg($org_id);
      foreach($data['featureList'] as $feature) {
        $subscriptionEntitlement = $feature->subscriptionEntitlement();
        if($subscriptionEntitlement->featureId == 'resource-creation' )
        $Limits['resourceLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'challenge-creation')
        $Limits['challengeLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'lab-creation')
        $Limits['labLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'resource-collection-creation')
        $Limits['resourceCollectionLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'resource-group-creation')
        $Limits['resourceGroupLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'challenge-path-creation')
        $Limits['challengePathLimit'] = $subscriptionEntitlement->value;
      }
      return $Limits;
    } catch (Exception $e) {
      return false;
    }
  }
  
   // get created things count (created lab, challenge, group count)
   public static function getCreatedValuesCount($id, $component)
   {
     try {
       $organisation = Organisation::where('id', $id)->first();
       $orgValueCount = [];
       if($component == 'lab' || $component == 'all')
       $orgValueCount['lab'] = Lab::where('organisation', $id)->where('is_auto_created', '0')->count();
       if($component == 'challenge' || $component == 'all')
       $orgValueCount['challenge'] = Challange::where(['organisation' => $id])->count();
       if($component == 'challenge_path' || $component == 'all')
       $orgValueCount['challenge_path'] = Group::where('organisation', $id)->where('type', 'challenge')->count();
       if($component == 'lab_program' || $component == 'all')
       $orgValueCount['lab_program'] = Group::where('organisation', $id)->where('type', 'lab')->count();
       if($component == 'resource_group' || $component == 'all')
       $orgValueCount['resource_group'] = Group::where('organisation', $id)->where('type', 'resource')->count();
       if($component == 'resource_module_count' || $component == 'all')
       $orgValueCount['resource_module_count'] = Resource::where('org_id', $id)->where('is_auto_created', '0')->count();
       if($component == 'resource_collection_count' || $component == 'all')
       $orgValueCount['resource_collection_count'] = ResourceGroup::where('org_id', $id)->count();
       if($component == 'org_org_manager_count' || $component == 'all')
       $orgValueCount['org_org_manager_count'] = OrganizationInviteUser::where('organisation_id', $id)->count();
       if($component == 'user_count' || $component == 'all')
       $orgValueCount['user_count'] = OrganizationInviteUser::where('organisation_id', $id)->where('role', 'user')->count();
       return $orgValueCount;
     } catch(Exception $e) {
       return false;
     } 
   }

}