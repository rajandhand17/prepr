<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event)
    {
       
        $message = $event->message;
        $to = $message->getTo();
        $from = $message->getFrom();
        $subject = $message->getSubject();
        $body = $message->getBody();
        EmailLog::create([
            'to' => $to[0]->getAddress(),
            'from' => $from[0]->getAddress(),
            'subject' => $subject,
            'body' => $body->getBody(),
        ]);
    }
}
