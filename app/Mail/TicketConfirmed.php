<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public ?PaymentTransaction $payment,
        public ?string $ticketUrl,
    ) {
    }

    public function build(): self
    {
        $this->ticket->loadMissing(['event', 'user', 'ticketCategory']);

        $qrCodeDataUri = null;
        if ($this->ticket->qr_code_path && Storage::disk('public')->exists($this->ticket->qr_code_path)) {
            $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode(
                Storage::disk('public')->get($this->ticket->qr_code_path)
            );
        }

        $mail = $this
            ->subject('Your WADO Ticket — ' . ($this->ticket->event?->title ?? 'Event Confirmed'))
            ->view('emails.tickets.confirmed', [
                'ticket'        => $this->ticket,
                'payment'       => $this->payment,
                'ticketUrl'     => $this->ticketUrl,
                'qrCodeDataUri' => $qrCodeDataUri,
            ]);

        try {
            $pdf = Pdf::loadView('pages.tickets.pdf_compact', [
                'ticket'        => $this->ticket,
                'qrCodeDataUri' => $qrCodeDataUri,
            ]);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'sans-serif',
            ]);
            $mail->attachData(
                $pdf->output(),
                'wado-ticket-' . $this->ticket->ticket_code . '.pdf',
                ['mime' => 'application/pdf']
            );
        } catch (\Throwable $e) {
            Log::warning('TicketConfirmed: PDF generation failed, sending without attachment', [
                'ticket_id' => $this->ticket->id,
                'error'     => $e->getMessage(),
            ]);
        }

        return $mail;
    }
}
