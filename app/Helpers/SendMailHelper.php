<?php

namespace App\Helpers;
use Mail;
use App\Mail\SendMail;

class SendMailHelper
{

    public static function sendMail($user,$view,$data)
    {
        try {
            $user->blade=$view;
            $user->subject=$data["subject"];

             $result= Mail::to($user->email)->send(new SendMail($user));

            if($result){
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}
