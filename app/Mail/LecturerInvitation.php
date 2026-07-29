<?php

namespace App\Mail;

use App\Models\LecturerInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LecturerInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LecturerInvite $invite,
        public string $activationUrl,
    ) {
    }

    public function build()
    {
        return $this
            ->subject('You have been added as a lecturer')
            ->view('emails.lecturer-invitation');
    }
}
