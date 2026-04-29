<?php

namespace App\Http\Controllers;

use App\Jobs\IssueTicketForPayment;
use App\Models\PaymentTransaction;
use App\Services\Payment\MarzePayService;
use App\Services\Payment\PaymentLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @group Webhooks
 *
 * Incoming callbacks from external payment providers. Called by MarzPay, not by the browser.
 */
class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, MarzePayService $marzePayService, PaymentLifecycleService $lifecycleService): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Marze-Signature')
            ?: $request->header('X-Signature')
            ?: $request->header('X-Webhook-Signature');

        if (! $marzePayService->verifyWebhookSignature($rawBody, $signature)) {
            return response()->json(['ok' => false, 'message' => 'Invalid webhook signature.'], 401);
        }

        $payload = $request->all();

        // MarzPay sends reference in transaction.reference
        $idempotencyKey = strtoupper((string) (
            data_get($payload, 'transaction.reference')
            ?? data_get($payload, 'idempotency_key')
            ?? data_get($payload, 'reference')
            ?? data_get($payload, 'metadata.idempotency_key')
            ?? data_get($payload, 'data.idempotency_key')
            ?? ''
        ));

        // MarzPay sends provider transaction ID in collection.provider_transaction_id
        $providerReference = (string) (
            data_get($payload, 'collection.provider_transaction_id')
            ?? data_get($payload, 'transaction.uuid')
            ?? data_get($payload, 'transaction_id')
            ?? data_get($payload, 'provider_reference')
            ?? data_get($payload, 'data.transaction_id')
            ?? data_get($payload, 'data.provider_reference')
            ?? ''
        );

        $payment = PaymentTransaction::query()
            ->when($idempotencyKey !== '', fn ($q) => $q->where('idempotency_key', $idempotencyKey))
            ->when($idempotencyKey === '' && $providerReference !== '', fn ($q) => $q->where('provider_reference', $providerReference))
            ->first();

        if (! $payment) {
            return response()->json(['ok' => false, 'message' => 'Payment transaction not found.'], 404);
        }

        $state = $marzePayService->resolveWebhookState($payload);
        $result = DB::transaction(function () use ($payment, $providerReference, $payload, $state, $lifecycleService): string {
            $locked = PaymentTransaction::query()->lockForUpdate()->findOrFail($payment->id);

            $locked->forceFill([
                'provider_reference' => $providerReference ?: $locked->provider_reference,
                'provider_status' => (string) (
                    data_get($payload, 'transaction.status')
                    ?? data_get($payload, 'event_type')
                    ?? data_get($payload, 'status')
                    ?? data_get($payload, 'data.status')
                    ?? $locked->provider_status
                ),
                'callback_received_at' => now(),
                'webhook_payload' => $payload,
            ])->save();

            if ($state === 'confirmed') {
                if ($locked->status === PaymentTransaction::STATUS_CONFIRMED) {
                    return 'Already confirmed.';
                }

                if ($locked->status === PaymentTransaction::STATUS_REFUNDED) {
                    return 'Already refunded.';
                }

                // Verify the amount MarzePay collected matches what we charged.
                // Protects against a scenario where payment was made for a
                // different amount than the ticket price.
                $paidAmount = (float) (
                    data_get($payload, 'collection.amount')
                    ?? data_get($payload, 'transaction.amount')
                    ?? data_get($payload, 'amount')
                    ?? data_get($payload, 'data.amount')
                    ?? 0
                );

                if ($paidAmount > 0 && abs($paidAmount - (float) $locked->total_amount) > 1) {
                    Log::error('Webhook amount mismatch — ticket not issued', [
                        'payment_id'      => $locked->id,
                        'expected_amount' => $locked->total_amount,
                        'received_amount' => $paidAmount,
                        'idempotency_key' => $locked->idempotency_key,
                    ]);

                    $locked->forceFill([
                        'last_error' => "Amount mismatch: expected {$locked->total_amount}, webhook reported {$paidAmount}.",
                    ])->save();

                    return 'Amount mismatch — ticket not issued.';
                }

                $locked->forceFill([
                    'status' => PaymentTransaction::STATUS_CONFIRMED,
                    'confirmed_at' => now(),
                    'failed_at' => null,
                    'last_error' => null,
                ])->save();

                IssueTicketForPayment::dispatch($locked->id);

                return 'Payment confirmed.';
            }

            if ($state === 'refunded') {
                $lifecycleService->markRefunded($locked->fresh(['ticket']), 'Payment refunded by provider.');

                return 'Payment refunded.';
            }

            if ($state === 'pending') {
                if ($locked->status === PaymentTransaction::STATUS_INITIATED) {
                    $locked->forceFill([
                        'status' => PaymentTransaction::STATUS_PENDING,
                    ])->save();
                }

                return 'Payment still pending.';
            }

            if ($locked->status === PaymentTransaction::STATUS_CONFIRMED) {
                return 'Already confirmed; ignoring failed callback.';
            }

            $lifecycleService->markFailedAndRelease($locked, (string) (data_get($payload, 'message') ?: 'Payment failed callback received.'));

            return 'Payment marked failed.';
        });

        return response()->json(['ok' => true, 'message' => $result]);
    }
}
