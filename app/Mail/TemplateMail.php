<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class TemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $templates = Array();
    public $email;
    public $signature = Array();
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    //public function __construct($client, $templates, $email, $signature)
    public function __construct($client, $templates, $subject = '', $email = '')
    {
        $this->client = $client;
        $this->templates = $templates;
        $this->subject = $subject;
        $this->email = $email;
        //$this->signature = $signature;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //$email = $this->view('emails.template')->with(['email' => $this->email,'signature' => $this->signature]);
        $email = $this->subject($this->subject)->view('emails.template')->with(['email' => $this->email]);
        /*return $this->view('emails.template')
                ->attach(storage_path('app/templates/'.$this->template));*/
        foreach($this->templates as $template):
            if($template['type'] == 'template')
                $email->attach(storage_path('app/templates/'.$template['file']));
            else
                $email->attach(storage_path('app/documents/'.$template['file']));

        endforeach;

        return $email;
    }
}
