<?php

namespace App\Mail;

use App\VerifyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verifyUser;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $verifyUser)
    {
        $this->user = $user;
        $this->verifyUser = $verifyUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verifyuser')->with(['name' => $this->user->first_name,'email' => $this->user->email,'token' => $this->verifyUser->token]);
    }
}
