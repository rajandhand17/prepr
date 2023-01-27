<?php
namespace App\Helpers;
use Twilio\Rest\Client;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\Master\SmsRequest;

class SMSHelper{

    public static function sendsms($receiver,$message)
    {   
       
        try {
            $accountSid = config('twilio.accountSid');
            $authToken = config('twilio.authToken');
            $twilioNumber =config('twilio.twilioNumber'); 
         
           $client = new Client($accountSid, $authToken);
 
           $result= $client->messages->create($receiver, [
                'from' => $twilioNumber,
                'body' => $message
            ]);
            if($result){
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}