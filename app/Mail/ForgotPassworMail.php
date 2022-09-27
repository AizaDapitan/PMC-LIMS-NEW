<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassworMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    public $emailSubject;
    public $view;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$emailSubject,$view)
    {
        $this->details = $details;
        $this->emailSubject = $emailSubject;
        $this->view = $view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)->view($this->view);
    }
}
