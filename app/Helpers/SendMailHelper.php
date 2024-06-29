<?php

namespace App\Helpers;

use App\Mail\SendMail;
use Mail;

class SendMailHelper
{
    public static function sendMail($user, $view, $data)
    {
        try {
            $result = Mail::to($user->email)->send(new SendMail($user, $view, $data));

            return $result;
            if ($result) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return $e;

            return false;
        }
    }
}
