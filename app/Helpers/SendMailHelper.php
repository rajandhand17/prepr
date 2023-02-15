<?php

namespace App\Helpers;
use Mail;
use App\Mail\SendMail;

class SendMailHelper
{

    public static function sendMail($user,$type)
    {
        try {
            if($type=="register_user"){
               $user->blade="email.reset_password";
               $user->subject="Verify User";    
            }
            if($type=="forget_password"){
                $user->blade="email.reset_password";
                $user->subject="Forget Password";
             }
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
