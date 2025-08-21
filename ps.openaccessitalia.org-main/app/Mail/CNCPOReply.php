<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CNCPOReply extends Mailable
{
    use Queueable, SerializesModels;

    public string $listaProg;

    public string $listaId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $listaProg,
        string $listaId
    ) {
        $this->listaProg = $listaProg;
        $this->listaId = $listaId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('CNCPO_PEC_EMAIL'))
            ->view('mail.cncpo-reply')
            ->text('mail.cncpo-reply-text');
    }
}
