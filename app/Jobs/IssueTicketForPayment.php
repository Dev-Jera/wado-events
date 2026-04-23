<?php

namespace App\Jobs;

use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Services\Admin\AdminIncidentNotificationService;
use App\Services\Ticket\TicketQrService;
use App\Jobs\SendTicketNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class IssueTicketForPayment implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    public function __construct(public int $paymentTransactionId)
    {
        $this->onQueue('tickets');
    }

    public function backoff(): array
    {
        return [10, 20, 30];
    }

    protected function extractPayerName(array $payload): ?string
    {
        $candidates = [
            'payer_name',
            'subscriber_name',
            'account_holder_name',
            'account_name',
            'sender_name',
            'data.payer_name',
            'data.subscriber_name',
            'data.account_holder_name',
            'data.account_name',
            'data.sender_name',
        ];

        foreach ($candidates as $key) {
            $value = data_get($payload, $key);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    public function handle(TicketQrService $ticketQrService): void
    {
        $payment = PaymentTransaction::query()
            ->with(['user', 'event', 'ticketCategory'])
            ->find($this->paymentTransactionId);

        if (! $payment || $payment->status !== PaymentTransaction::STATUS_CONFIRMED || $payment->ticket_id) {
            return;
        }

        $ticket = DB::transaction(function () use ($payment, $ticketQrService) {
            $locked = PaymentTransaction::query()->lockForUpdate()->findOrFail($payment->id);

            if ($locked->status !== PaymentTransaction::STATUS_CONFIRMED || $locked->ticket_id) {
                return $locked->ticket;
            }

            $ticket = Ticket::query()->create([
                'user_id' => $locked->user_id,
                'event_id' => $locked->event_id,
                'ticket_category_id' => $locked->ticket_category_id,
                'holder_name' => $locked->holder_name,
                'payer_name' => $this->extractPayerName((array) ($locked->webhook_payload ?? [])),
                'ticket_code' => $ticketQrService->generateUniqueTicketCode($locked->user_id, $locked->event_id),
                'qr_code_path' => null,
                'quantity' => $locked->quantity,
                'unit_price' => $locked->unit_price,
                'total_amount' => $locked->total_amount,
                'payment_provider' => $locked->payment_provider,
                'status' => Ticket::STATUS_CONFIRMED,
                'purchased_at' => now(),
            ]);

            $ticket->forceFill([
                'qr_code_path' => $ticketQrService->generateAndStoreForTicket($ticket),
            ])->save();

            $locked->forceFill([
                'ticket_id' => $ticket->id,
                'ticket_issued_at' => now(),
            ])->save();

            return $ticket;
        });

        if ($ticket instanceof Ticket) {
            SendTicketNotification::dispatch($ticket->id, $payment->id);
        }
    }

    public function failed(Throwable $exception): void
    {
        $payment = PaymentTransaction::query()
            ->with(['user', 'event', 'ticket'])
            ->find($this->paymentTransactionId);

        Log::error('IssueTicketForPayment exhausted retries', [
            'payment_transaction_id' => $this->paymentTransactionId,
            'payment_status' => $payment?->status,
            'ticket_id' => $payment?->ticket_id,
            'event_id' => $payment?->event_id,
            'user_id' => $payment?->user_id,
            'error' => $exception->getMessage(),
        ]);

        if (! $payment) {
            return;
        }

        $payment->forceFill([
            'last_error' => 'Ticket issuance failed after retries: ' . $exception->getMessage(),
        ])->save();

        app(AdminIncidentNotificationService::class)
            ->notifyFailedPayment($payment->fresh(['event', 'user']), 'Ticket issuance failed after retries. Manual intervention required.');
    }
}
