<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('admin@admin.com', 'Admin')
            ->subject('Complete profile')
            ->markdown('mails.verification')
            ->with([
                'name' => 'You are successfully verified please follow this link to complete your Profile. ',
                'link' => 'localhost:8000/api/profileComplete'
            ]);
    }
}
