<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;


    public $message;
    public $link;

    public function __construct($message, $link)
    {
        $this->message = $message;
        $this->link = $link;
    }

    public function build()
    {
        return $this->view('emails.notification-mail')
            ->with([
                'message' => $this->message,
                'link' => $this->link,
                'user' => auth()->user()->first_name." ".auth()->user()->last_name
            ]);
    }
}
