<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public User $user,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Event Submitted — ' . $this->event->title)
            ->view('emails.events.submitted');
    }
}
