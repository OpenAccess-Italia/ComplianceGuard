<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CNCPOReply extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public string $id,
        public string $data
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(\Settings::get(\App\SettingKeys::CNCPO_PEC_EMAIL))
            ->view('mail.cncpo-reply')
            ->text('mail.cncpo-reply-text');
    }
}
