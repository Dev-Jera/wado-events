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
        public ?string $qrCodeDataUri,
        public ?string $pdfContent = null
    ) {
    }

    public function build(): self
    {
        $mail = $this
            ->subject('Your WADO Ticket — ' . ($this->ticket->event?->title ?? 'Event Confirmed'))
            ->view('emails.tickets.confirmed', [
                'ticket'       => $this->ticket,
                'payment'      => $this->payment,
                'ticketUrl'    => $this->ticketUrl,
                'qrCodeDataUri' => $this->qrCodeDataUri,
            ]);

        if ($this->pdfContent) {
            $mail->attachData(
                $this->pdfContent,
                'wado-ticket-' . $this->ticket->ticket_code . '.pdf',
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}
