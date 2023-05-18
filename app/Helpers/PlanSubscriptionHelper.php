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
    public static function createCustomer($user, $language) {
      try {
        Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
        $result = Customer::create(array(
          "firstName" =>  $user->first_name,
          "lastName" => $user->last_name,
          "email" => $user->email,
          "locale" => $language,
          ));
        $customer = $result->customer();
        $card = $result->card();
        return $customer;
      } catch(Exception $e) {
        return false;
      }
    }
    
    //assigning free plan to new user when he register or create new org  //subscribePlan
    public static function subscribePlan($customer, $plan_name, $org_id) {
      try {
        Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
        $result = Subscription::createWithItems($customer->id,array(
          "subscriptionItems" => array(array(
            "itemPriceId" => $plan_name,
            "unitPrice" => 0,
            "quantity" => 1,
          )
          ),
          "cf_org_id" => $org_id,
          ));
        $subscription = $result->subscription();
      } catch(Exception $e) {
        return false;
      }
    }

        // get customer id
        public static function getCustomer($userEmail) {
          try {
            Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
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
          } catch(Exception $e) {
            return false;
          }
        }
         // getSubscribedPlanDetails  //getSubscribedPlanForOrg
    //fetch subscription and their features limit based on org id
  public static function getSubscribedPlanDetailForOrg($org_id)
  {
      try {
          Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
              $all_Subscription = Subscription::all(array(
                  "cf_org_id[is]" => $org_id
                      ));
          if($all_Subscription->count() > 0){
              foreach($all_Subscription as $entry) {
                $subscription = $entry->subscription();
                  // $customer = $entry->customer();
                  // $card = $entry->card();
                  $alll = SubscriptionEntitlement::subscriptionEntitlementsForSubscription( $subscription->id);
              }
              $data = ['featureList' => $alll, 'subscriptionDetail' => $subscription];
              return $data;
          }
          else {
              return $data = [];
          } 
      } catch (Exception $e) {
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
        $Limits['resourceModuleLimit'] = $subscriptionEntitlement->value;
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
        if($subscriptionEntitlement->featureId == 'lab-program-creation')
        $Limits['labProgramLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'organisation-manager-invite')
        $Limits['managerInviteLimit'] = $subscriptionEntitlement->value;
        if($subscriptionEntitlement->featureId == 'user-invite')
        $Limits['userInviteLimit'] = $subscriptionEntitlement->value;
      }
      return $Limits;
    } catch (Exception $e) {
      return false;
    }
  }

   // get Addons limits (created lab, challenge, group count)
   public static function getAddonsLimits($org_id) 
   {
     try{
       Environment::configure(config('chargebee.chargebee_site'), config('chargebee.chargebee_key'));
       $all_Subscription = Subscription::all(array(
         "cf_org_id[is]" => $org_id
         ));
       if($all_Subscription->count() > 0) {
         $addon = [];
         $subscription = $all_Subscription[0]->subscription();
         foreach ($subscription->subscriptionItems as $item) {
           if ($item->itemType === 'addon'){
             if($item->itemPriceId == 'challenge-creation-CAD-Monthly')
             $addon['challengeLimit'] = $item->quantity;
             elseif($item->itemPriceId == 'Resource-Creation-CAD-Monthly')
             $addon['resourceModuleLimit'] = $item->quantity;
             elseif($item->itemPriceId == 'lab-creation-CAD-Monthly')
             $addon['labLimit'] = $item->quantity;
           }
         }
         return $addon;
       }
     }catch(Exception $e){
       return false;
     }
 }

  // get total limits (created lab, challenge, group count)
  public static function getTotalLimits($org_id) 
  {
    try {
     $featureLimit = self::getFeatureLimits($org_id);
     $addonLimit = self::getAddonsLimits($org_id);
     $totalLimit = [];
     $totalLimit['challengeLimit'] = (array_key_exists('challengeLimit', $featureLimit) ? $featureLimit['challengeLimit'] : 0) + (array_key_exists('challengeLimit', $addonLimit) ?  $addonLimit['challengeLimit'] : 0);
     $totalLimit['labLimit'] =  (array_key_exists('labLimit', $featureLimit) ? $featureLimit['labLimit'] : 0) + (array_key_exists('labLimit', $addonLimit) ?  $addonLimit['labLimit'] : 0);
     $totalLimit['resourceModuleLimit'] = (array_key_exists('resourceModuleLimit', $featureLimit) ? $featureLimit['resourceModuleLimit'] : 0) + (array_key_exists('resourceModuleLimit', $addonLimit) ?  $addonLimit['resourceModuleLimit'] : 0);
     $totalLimit['labProgramLimit'] = (array_key_exists('labProgramLimit', $featureLimit) ? $featureLimit['labProgramLimit'] : 0) + (array_key_exists('resourceModuleLimit', $addonLimit) ?  $addonLimit['resourceModuleLimit'] : 0);
     $totalLimit['challengePathLimit'] = (array_key_exists('challengePathLimit', $featureLimit) ? $featureLimit['challengePathLimit'] : 0) + (array_key_exists('challengePathLimit', $addonLimit) ?  $addonLimit['challengePathLimit'] : 0);
     $totalLimit['resourceGroupLimit'] = (array_key_exists('resourceGroupLimit', $featureLimit) ? $featureLimit['resourceGroupLimit'] : 0) + (array_key_exists('resourceGroupLimit', $addonLimit) ?  $addonLimit['resourceGroupLimit'] : 0);
     $totalLimit['resourceCollectionLimit'] = (array_key_exists('resourceCollectionLimit', $featureLimit) ? $featureLimit['resourceCollectionLimit'] : 0) + (array_key_exists('resourceCollectionLimit', $addonLimit) ?  $addonLimit['resourceCollectionLimit'] : 0);
     $totalLimit['managerInviteLimit'] = (array_key_exists('managerInviteLimit', $featureLimit) ? $featureLimit['managerInviteLimit'] : 0) + (array_key_exists('managerInviteLimit', $addonLimit) ?  $addonLimit['managerInviteLimit'] : 0);
     $totalLimit['userInviteLimit'] = (array_key_exists('userInviteLimit', $featureLimit) ? $featureLimit['userInviteLimit'] : 0) + (array_key_exists('resourceCollectionLimit', $addonLimit) ?  $addonLimit['resourceCollectionLimit'] : 0);
     return $totalLimit;
   } catch (Exception $e) {
        return false;
     }
  }
  
  // get created things count (created lab, challenge, group count)  //getCreatedValuesCount
  public static function getFeatureUsage($id, $component)
  {
    try {
      $organisation = Organisation::where('id', $id)->first();
      $orgValueCount = [];
      if($component == 'lab' || $component == 'all')
      $orgValueCount['lab'] = Lab::where('organisation', $id)->where('is_auto_created', '0')->count();
      if($component == 'challenge' || $component == 'all')
      $orgValueCount['challenge'] = Challange::where(['organisation' => $id])->count();
      if($component == 'challenge_path' || $component == 'group')
      $orgValueCount['challenge_path'] = Group::where('organisation', $id)->where('type', 'challenge')->count();
      if($component == 'lab_program' || $component == 'group')
      $orgValueCount['lab_program'] = Group::where('organisation', $id)->where('type', 'lab')->count();
      if($component == 'resource_group' || $component == 'group')
      $orgValueCount['resource_group'] = Group::where('organisation', $id)->where('type', 'resource')->count();
      if($component == 'resource_module_count' || $component == 'resource')
      $orgValueCount['resource_module_count'] = Resource::where('org_id', $id)->where('is_auto_created', '0')->count();
      if($component == 'resource_collection_count' || $component == 'resource')
      $orgValueCount['resource_collection_count'] = ResourceGroup::where('org_id', $id)->count();
      if($component == 'org_org_manager_count' || $component == 'managerUsersCount')
      $orgValueCount['org_org_manager_count'] = OrganizationInviteUser::where('organisation_id', $id)->count();
      if($component == 'user_count' || $component == 'managerUsersCount') {
        $orgValueCount['userorg_count'] = OrganizationInviteUser::where('organisation_id', $id)->where('role', 'user')->count();
        $labs= Lab::select('id')->where('organisation', $id)->where('is_auto_created', '0')->pluck('id');
        $orgValueCount['user_count'] = MemberManagement::whereIn('module_id', $labs)->count();
        $orgValueCount['user_count']=  $orgValueCount['userorg_count'] + $orgValueCount['user_count'];
      }
      
      return $orgValueCount;
    } catch(Exception $e) {
      return false;
    } 
  }

}