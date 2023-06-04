<?php

namespace App\Mail;

use App\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientSaved extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $process_id;
    public $step_id;

    public function __construct(Client $client,$process_id,$step_id)
    {
        $this->client = $client;
        $this->process_id = $process_id;
        $this->step_id = $step_id;
    }

    public function build()
    {
        return $this->markdown('emails.client-saved')->with(['client' => $this->client,'process_id'=>$this->process_id,'step_id'=>$this->step_id]);
    }
}
