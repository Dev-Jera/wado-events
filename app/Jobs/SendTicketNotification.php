<?php

namespace App\Jobs;

use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Services\Payment\PaymentNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendTicketNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 90;

    public function __construct(
        public int  $ticketId,
        public ?int $paymentTransactionId = null,
    ) {
        $this->onQueue('notifications');
    }

    public function backoff(): array
    {
        return [30, 60];
    }

    public function handle(PaymentNotificationService $notificationService): void
    {
        $ticket = Ticket::query()->with(['user', 'event', 'ticketCategory'])->find($this->ticketId);

        if (! $ticket) {
            Log::warning('SendTicketNotification: ticket not found, skipping', [
                'ticket_id'  => $this->ticketId,
                'payment_id' => $this->paymentTransactionId,
            ]);
            return;
        }

        if ($this->paymentTransactionId !== null) {
            $payment = PaymentTransaction::query()->find($this->paymentTransactionId);

            if (! $payment) {
                Log::warning('SendTicketNotification: payment not found, skipping', [
                    'ticket_id'  => $this->ticketId,
                    'payment_id' => $this->paymentTransactionId,
                ]);
                return;
            }

            $notificationService->sendTicketConfirmed($ticket, $payment);
        } else {
            // Free ticket — no payment record
            $notificationService->sendFreeTicketConfirmed($ticket);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('SendTicketNotification exhausted retries', [
            'ticket_id'  => $this->ticketId,
            'payment_id' => $this->paymentTransactionId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
