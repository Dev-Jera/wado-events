<?php

namespace App\Services\Payment;

use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\TicketCategory;
use App\Services\Admin\AdminIncidentNotificationService;
use Illuminate\Support\Facades\DB;

class PaymentLifecycleService
{
    public function __construct(protected AdminIncidentNotificationService $incidentNotifications)
    {
    }

    public function releaseReservation(PaymentTransaction $payment): void
    {
        DB::transaction(function () use ($payment): void {
            $category = TicketCategory::query()->lockForUpdate()->find($payment->ticket_category_id);
            $event = Event::query()->lockForUpdate()->find($payment->event_id);

            if ($category) {
                $category->increment('tickets_remaining', $payment->quantity);
            }

            if ($event) {
                $event->increment('tickets_available', $payment->quantity);
            }
        });
    }

    public function markFailedAndRelease(PaymentTransaction $payment, string $message): void
    {
        if (! in_array($payment->status, [
            PaymentTransaction::STATUS_INITIATED,
            PaymentTransaction::STATUS_PENDING,
        ], true)) {
            return;
        }

        $this->releaseReservation($payment);

        $payment->forceFill([
            'status' => PaymentTransaction::STATUS_FAILED,
            'failed_at' => now(),
            'last_error' => $message,
        ])->save();

        $payment->loadMissing(['event', 'user']);
        $this->incidentNotifications->notifyFailedPayment($payment, $message);
    }

    public function markRefunded(PaymentTransaction $payment, string $message): void
    {
        if ($payment->status === PaymentTransaction::STATUS_REFUNDED) {
            return;
        }

        if ($payment->ticket && $payment->ticket->status !== 'cancelled') {
            $payment->ticket->forceFill([
                'status' => 'cancelled',
            ])->save();

            if ($payment->ticket->used_at) {
                $message .= ' Ticket had already been scanned.';
            }
        } elseif (! $payment->ticket_id) {
            $this->releaseReservation($payment);
        }

        $payment->forceFill([
            'status' => PaymentTransaction::STATUS_REFUNDED,
            'refunded_at' => now(),
            'last_error' => $message,
        ])->save();
    }

    public function expireIfTimedOut(PaymentTransaction $payment): void
    {
        if ($payment->status !== PaymentTransaction::STATUS_PENDING) {
            return;
        }

        if ($payment->expires_at && $payment->expires_at->isFuture()) {
            return;
        }

        $this->markFailedAndRelease($payment, 'Payment timed out after waiting for PIN confirmation.');
    }
}
