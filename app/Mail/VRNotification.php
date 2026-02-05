<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VRNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $vr;
    public $ticket;
    public $title;
    public $message;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $ticket, $title, $message, $type = 'info', $vr = null)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->vr = $vr;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "[VR Notification] " . $this->title;

        return $this->subject($subject)
            ->view('emails.vr-notification')
            ->with([
                'user' => $this->user,
                'ticket' => $this->ticket,
                'vr' => $this->vr,
                'title' => $this->title,
                'messageContent' => $this->message,
                'type' => $this->type,
            ]);
    }
}
