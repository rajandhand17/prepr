<?php
namespace App\Helpers;
use Twilio\Rest\Client;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\Master\SmsRequest;

class SMSHelper{

    public static function sendsms($receiver,$message)
    {   
       
        try {
            $accountSid = getenv("TWILIO_SID");
            $authToken = getenv("TWILIO_TOKEN");
            $twilioNumber = getenv("TWILIO_NUMBER");
         
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