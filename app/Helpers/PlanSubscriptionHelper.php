<?php

namespace App\Helpers;

use Carbon\Carbon;
use Session;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Exception;
use App\Models\Lab;
use Illuminate\Support\Facades\App;
use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\Subscription;
use ChargeBee\ChargeBee\Models\Customer;
use ChargeBee\ChargeBee\Models\ItemEntitlement;



class PlanSubscriptionHelper
{
    // create customer everytime when new user register 
    public static function createCustomer($user, $language) {
        Environment::configure("preprme-test","test_b9uO7I3n4qXg20G7sMfkiyEhwQYdNbd9");
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
    Environment::configure("preprme-test","test_b9uO7I3n4qXg20G7sMfkiyEhwQYdNbd9");
    $result = Subscription::createWithItems($customer->id,array(
      "subscriptionItems" => array(array(
        "itemPriceId" => "free-plan-CAD-Yearly",
        "unitPrice" => 0,
        "quantity" => 1)
      )
      ));
      $subscription = $result->subscription();
    }
}