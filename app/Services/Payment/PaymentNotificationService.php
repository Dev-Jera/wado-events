<?php

namespace App\Services\Payment;

use App\Mail\TicketConfirmed;
use App\Models\EmailLog;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    public function sendEmail(Ticket $ticket, ?PaymentTransaction $payment): void
    {
        try {
            $recipient = (string) ($ticket->user?->email ?? '');
            if ($recipient === '') {
                return;
            }

            $ticket = $ticket->fresh(['event', 'user', 'ticketCategory']);
            $ticketUrl = route('tickets.show', $ticket);
            $subject = 'Your WADO Ticket — ' . ($ticket->event?->title ?? 'Event Confirmed');

            Mail::to($recipient)->queue(new TicketConfirmed($ticket, $payment, $ticketUrl));

            EmailLog::create([
                'ticket_id' => $ticket->id,
                'recipient' => $recipient,
                'subject'   => $subject,
                'status'    => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::error('TicketConfirmed: email notification failed', [
                'ticket_id' => $ticket->id ?? null,
                'error'     => $e->getMessage(),
            ]);

            EmailLog::create([
                'ticket_id' => $ticket->id ?? null,
                'recipient' => $recipient ?? '',
                'subject'   => 'Ticket Confirmation',
                'status'    => 'failed',
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
