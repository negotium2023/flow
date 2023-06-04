<?php

namespace App\Mail;

use App\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientBasketEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $client;
    public $process_id;
    public $process_name;
    public $step_id;
    public $password;
    public $email;
    public function __construct(Client $client,$process_name,$process_id,$step_id,$password,$email)
    {
        $this->client = $client;
        $this->process_id = $process_id;
        $this->process_name = $process_name;
        $this->step_id = $step_id;
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('KYC procedural requirements')->view('emails.clientbasket')->with(['email'=>$this->email,'process_name'=>$this->process_name,'clientid'=>$this->client->id,'process_id'=>$this->process_id,'step_id'=>$this->step_id,'password'=>$this->password]);
    }
}
