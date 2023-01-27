<?php

namespace App\Listeners;

use App\Events\Events;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class SendEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Events  $event
     * @return void
     */
    public function handle(Events $event)
    {   
        $user = User::where("id",$event->userId)->first();
        $user['otp']=$user->two_factor_otp;
        $mail_data=[
            "recipient"=>$user['email'],
            "fromEmail"=>env("MAIL_USERNAME"),
            "fromName"=>$user['name'],
            "subject"=>"Forget Password!",
        ];
        \Mail::send('email.reset_password',["user"=>$user],function($message) use ($mail_data){
            $message->to($mail_data['recipient'])->from($mail_data['fromEmail'],$mail_data['fromName'])->subject($mail_data['subject']);
        });    

    }
}
