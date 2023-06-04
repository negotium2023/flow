<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HelpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $page;
    public $comment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $page, $comment)
    {
        $this->user = $user;
        $this->page = $page;
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.help');
    }
}
