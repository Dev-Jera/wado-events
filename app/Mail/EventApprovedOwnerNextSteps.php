<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventApprovedOwnerNextSteps extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public User $owner,
        public string $dashboardAlias,
        public string $dashboardLoginUrl,
        public string $setPasswordUrl,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Your event is approved — next steps on WADO Ticketing')
            ->view('emails.events.approved-owner-next-steps');
    }
}
