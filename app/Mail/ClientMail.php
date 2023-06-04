<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;

    public function __construct($data)
    {
        $this->subject = $data['subject'];
        $this->message = $data['message'];
    }

    public function build()
    {
        //  dd($this->message);

        return $this->subject($this->subject)->view('emails.client-mail')->with(['mail_message' => $this->message]);
    }
}
