<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailSummaryLabMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $summaryData;
    public $summeryContent;
    public $summeryType;
    public $user;

    public function __construct($summaryData, $summeryContent, $summeryType, $user)
    {
        $this->summaryData = $summaryData;
        $this->summeryContent = $summeryContent;
        $this->summeryType = $summeryType;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->summeryContent['subjectnetwork'])->view('email.email-summary.email-summary-lab-mail');
    }
}
