<?php

namespace App\Services\Payment;

use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\TicketCategory;
use App\Services\Admin\AdminIncidentNotificationService;
use Illuminate\Support\Facades\DB;

class PaymentLifecycleService
{
    public function __construct(
        protected AdminIncidentNotificationService $incidentNotifications,
        protected MarzePayService $marzePayService
    )
    {
    }

    public function refundWithProvider(PaymentTransaction $payment, string $reason): array
    {
        if ($payment->status === PaymentTransaction::STATUS_REFUNDED) {
            return [
                'ok' => true,
                'message' => 'Payment is already refunded.',
            ];
        }

        if (! in_array($payment->status, [
            PaymentTransaction::STATUS_CONFIRMED,
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_INITIATED,
        ], true)) {
            return [
                'ok' => false,
                'message' => 'This payment status is not eligible for refund.',
            ];
        }

        $providerResult = $this->marzePayService->requestRefund($payment, $reason);

        if (! ($providerResult['ok'] ?? false)) {
            $payment->forceFill([
                'last_error' => 'MarzPay refund failed: ' . (string) ($providerResult['message'] ?? 'Unknown provider error.'),
                'refund_request_status' => $payment->refund_requested_at ? 'FAILED_PROVIDER' : $payment->refund_request_status,
            ])->save();

            return [
                'ok' => false,
                'message' => (string) ($providerResult['message'] ?? 'MarzPay refund failed.'),
            ];
        }

        $providerPayload = (array) ($payment->provider_payload ?? []);
        $providerPayload['refund'] = [
            'requested_at' => now()->toIso8601String(),
            'reason' => $reason,
            'response' => $providerResult['payload'] ?? null,
            'provider_status' => $providerResult['provider_status'] ?? null,
        ];

        $payment->forceFill([
            'provider_payload' => $providerPayload,
            'provider_status' => strtoupper((string) ($providerResult['provider_status'] ?? 'REFUND_ACCEPTED')),
            'refund_request_status' => $payment->refund_requested_at ? 'PROCESSED' : $payment->refund_request_status,
        ])->save();

        $this->markRefunded($payment->fresh(['ticket']), $reason);

        return [
            'ok' => true,
            'message' => (string) ($providerResult['message'] ?? 'Refund accepted by MarzPay and recorded.'),
        ];
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

        $payment->loadMissing('ticket');
        $shouldRestoreInventory = false;

        if ($payment->ticket && $payment->ticket->status !== 'cancelled') {
            $payment->ticket->forceFill([
                'status' => 'cancelled',
            ])->save();

            if ($payment->ticket->used_at) {
                $message .= ' Ticket had already been scanned.';
            } else {
                $shouldRestoreInventory = true;
            }
        } elseif (! $payment->ticket_id && in_array($payment->status, [
            PaymentTransaction::STATUS_INITIATED,
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_CONFIRMED,
        ], true)) {
            $shouldRestoreInventory = true;
        }

        if ($shouldRestoreInventory) {
            $this->releaseReservation($payment);
        }

        $payment->forceFill([
            'status' => PaymentTransaction::STATUS_REFUNDED,
            'refunded_at' => now(),
            'last_error' => $message,
            'refund_request_status' => $payment->refund_requested_at ? 'PROCESSED' : $payment->refund_request_status,
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
