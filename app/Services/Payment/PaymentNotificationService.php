<?php

namespace App\Services\Payment;

use App\Mail\TicketConfirmed;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PaymentNotificationService
{
    public function sendTicketConfirmed(Ticket $ticket, PaymentTransaction $payment): void
    {
        $this->sendEmail($ticket, $payment);
        $this->sendSms($ticket, $payment);
    }

    public function sendFreeTicketConfirmed(Ticket $ticket): void
    {
        $this->sendEmail($ticket, null);
    }

    protected function sendEmail(Ticket $ticket, ?PaymentTransaction $payment): void
    {
        try {
            $recipient = (string) ($ticket->user?->email ?? '');
            if ($recipient === '') {
                return;
            }

            $ticket = $ticket->fresh(['event', 'user', 'ticketCategory']);

            $ticketUrl = route('tickets.show', $ticket);

            $qrCodeDataUri = null;
            if ($ticket->qr_code_path && Storage::disk('public')->exists($ticket->qr_code_path)) {
                $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode(
                    Storage::disk('public')->get($ticket->qr_code_path)
                );
            }

            $pdfContent = null;
            try {
                $pdf = Pdf::loadView('pages.tickets.pdf_compact', [
                    'ticket'        => $ticket,
                    'qrCodeDataUri' => $qrCodeDataUri,
                ]);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'sans-serif',
                ]);
                $pdfContent = $pdf->output();
            } catch (\Throwable $e) {
                Log::warning('TicketConfirmed: PDF generation failed, sending email without attachment', [
                    'ticket_id' => $ticket->id,
                    'error'     => $e->getMessage(),
                ]);
            }

            Mail::to($recipient)->queue(new TicketConfirmed(
                $ticket,
                $payment,
                $ticketUrl,
                $qrCodeDataUri,
                $pdfContent
            ));
        } catch (\Throwable $e) {
            Log::error('TicketConfirmed: email notification failed', [
                'ticket_id' => $ticket->id ?? null,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    protected function sendSms(Ticket $ticket, PaymentTransaction $payment): void
    {
        $apiKey = (string) config('services.africas_talking.api_key', '');
        $username = (string) config('services.africas_talking.username', '');
        $from = (string) config('services.africas_talking.from', '');
        $baseUrl = rtrim((string) config('services.africas_talking.base_url', 'https://api.africastalking.com'), '/');

        if ($apiKey === '' || $username === '') {
            Log::info('SMS notification skipped - Africa\'s Talking credentials not configured', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        $rawPhone = (string) ($payment->phone_number ?: ($ticket->user?->phone ?? ''));
        $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
        if ($digits === '') {
            Log::warning('SMS notification skipped - missing destination phone number', [
                'ticket_id' => $ticket->id,
                'payment_id' => $payment->id,
            ]);

            return;
        }

        $phone = str_starts_with($digits, '256')
            ? ('+' . $digits)
            : ('+256' . ltrim($digits, '0'));

        $message = sprintf(
            'WADO: Payment confirmed. Ticket %s for %s on %s at %s.',
            $ticket->ticket_code,
            (string) ($ticket->event?->title ?? 'your event'),
            (string) ($ticket->event?->starts_at?->format('d M H:i') ?? 'TBD'),
            (string) ($ticket->event?->venue ?? 'venue TBC')
        );

        $payload = [
            'username' => $username,
            'to' => $phone,
            'message' => $message,
        ];

        if ($from !== '') {
            $payload['from'] = $from;
        }

        $response = Http::timeout(20)
            ->asForm()
            ->withHeaders([
                'apiKey' => $apiKey,
                'Accept' => 'application/json',
            ])
            ->post($baseUrl . '/version1/messaging', $payload);

        if (! $response->successful()) {
            Log::error('SMS notification failed', [
                'ticket_id' => $ticket->id,
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return;
        }

        Log::info('SMS notification sent', [
            'ticket_id' => $ticket->id,
            'payment_id' => $payment->id,
            'to' => $phone,
        ]);
    }
}
