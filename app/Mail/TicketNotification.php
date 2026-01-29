<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ticket;
    public $title;
    public $message;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Ticket $ticket, string $title, string $message, string $type = 'info')
    {
        $this->user = $user;
        $this->ticket = $ticket;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.ticket-notification')
            ->with([
                'user' => $this->user,
                'ticket' => $this->ticket,
                'title' => $this->title,
                'message' => $this->message,
                'type' => $this->type,
            ]);
    }
}
