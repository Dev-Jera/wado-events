<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public ?PaymentTransaction $payment,
        public ?string $ticketUrl,
        public ?string $qrCodeDataUri
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Your WADO ticket is confirmed')
            ->markdown('emails.tickets.confirmed', [
                'ticket' => $this->ticket,
                'payment' => $this->payment,
                'ticketUrl' => $this->ticketUrl,
                'qrCodeDataUri' => $this->qrCodeDataUri,
            ]);
    }
}
