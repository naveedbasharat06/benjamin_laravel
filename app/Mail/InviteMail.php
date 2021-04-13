<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteMail extends Mailable
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
            ->subject('Invitation to join our team')
            ->markdown('mails.invite_mail')
            ->with([
                'name' => 'Click below link to process Registration ',
                'link' => 'localhost:8000/api/inviteResponce'
            ]);
    }
}
