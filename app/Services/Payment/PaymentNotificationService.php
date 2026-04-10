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
        // SMS is currently not sent through MarzPay API
        // Relying on email notifications instead
        Log::info('SMS notification skipped - not configured with MarzPay v1 API', [
            'ticket_id' => $ticket->id,
        ]);
    }
}
