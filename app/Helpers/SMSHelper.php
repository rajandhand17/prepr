<?php

namespace App\Helpers;

use Twilio\Rest\Client;

class SMSHelper
{
    public static function sendSms($receiver, $message)
    {
        try {
            $account_sid = config('twilio.account_sid');
            $auth_token = config('twilio.auth_token');
            $twilio_number = config('twilio.twilio_number');

            $client = new Client($account_sid, $auth_token);

            $result = $client->messages->create($receiver, [
                'from' => $twilio_number,
                'body' => $message,
            ]);
            if ($result) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
