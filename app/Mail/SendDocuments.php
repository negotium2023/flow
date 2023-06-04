<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDocuments extends Mailable
{
    use Queueable, SerializesModels;
    public $documents;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($documents, array $email)
    {
        $this->documents = $documents;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $my_documents = $this->from(auth()->user()->email)
            ->subject($this->email['subject']??'Your Documents')
            ->view('emails.senddocuments');

        foreach ($this->documents as $document){
            $my_documents->attach($document);
        }

        return $my_documents->with($this->email);
    }
}
