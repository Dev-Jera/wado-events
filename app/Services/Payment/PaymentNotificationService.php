<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentNotificationService
{
    public function sendTicketConfirmed(Ticket $ticket, PaymentTransaction $payment): void
    {
        $this->sendEmail($ticket);
        $this->sendSms($ticket, $payment);
    }

    protected function sendEmail(Ticket $ticket): void
    {
        $recipient = (string) ($ticket->user?->email ?? '');
        if ($recipient === '') {
            return;
        }

        $ticketUrl = route('tickets.show', $ticket);

        Mail::raw(
            "Your payment was confirmed. Ticket code: {$ticket->ticket_code}. View ticket: {$ticketUrl}",
            function ($message) use ($recipient): void {
                $message->to($recipient)->subject('Ticket confirmed - WADO');
            }
        );
    }

    protected function sendSms(Ticket $ticket, PaymentTransaction $payment): void
    {
        $smsEndpoint = (string) config('services.marzepay.sms_endpoint', '');
        $phone = (string) ($payment->phone_number ?: $ticket->user?->phone ?: '');

        if ($smsEndpoint === '' || $phone === '') {
            Log::info('SMS notification skipped', [
                'ticket_id' => $ticket->id,
                'phone' => $phone,
            ]);

            return;
        }

        $message = sprintf(
            'Payment confirmed. Ticket %s is ready. Open: %s',
            $ticket->ticket_code,
            route('tickets.show', $ticket)
        );

        Http::timeout((int) config('services.marzepay.timeout', 30))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'X-API-KEY' => (string) config('services.marzepay.api_key', ''),
                'X-API-SECRET' => (string) config('services.marzepay.api_secret', ''),
            ])
            ->post($smsEndpoint, [
                'to' => $phone,
                'sender_id' => (string) config('services.marzepay.sms_sender_id', 'WADO'),
                'message' => $message,
            ]);
    }
}
