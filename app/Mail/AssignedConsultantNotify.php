<?php

namespace App\Mail;

use App\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignedConsultantNotify extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('A Client has been assigned to you')->view('emails.assignconsultantnotify')->with(['name'=>($this->client->company != null ? $this->client->company : $this->client->first_name.' '.$this->client->last_name),'id'=>$this->client->id]);
    }
}
