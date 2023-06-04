<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrialNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $days_left;
    public $user;
    public function __construct($days_left,User $user)
    {
        $this->days_left = $days_left;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.trialnotification')->with([
            'days_left' => $this->days_left,
            'user' => $this->user
        ]);
    }
}
