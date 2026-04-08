<?php

namespace App\Http\Controllers;

use App\Jobs\IssueTicketForPayment;
use App\Models\PaymentTransaction;
use App\Services\Payment\MarzePayService;
use App\Services\Payment\PaymentLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $idempotencyKey = strtoupper((string) (
            data_get($payload, 'idempotency_key')
            ?? data_get($payload, 'reference')
            ?? data_get($payload, 'metadata.idempotency_key')
            ?? data_get($payload, 'data.idempotency_key')
            ?? ''
        ));

        $providerReference = (string) (
            data_get($payload, 'transaction_id')
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

        $payment->forceFill([
            'provider_reference' => $providerReference ?: $payment->provider_reference,
            'provider_status' => (string) (data_get($payload, 'status') ?? data_get($payload, 'data.status') ?? $payment->provider_status),
            'callback_received_at' => now(),
            'webhook_payload' => $payload,
        ])->save();

        $state = $marzePayService->resolveWebhookState($payload);

        if ($state === 'confirmed') {
            if ($payment->status === PaymentTransaction::STATUS_CONFIRMED) {
                return response()->json(['ok' => true, 'message' => 'Already confirmed.']);
            }

            if ($payment->status === PaymentTransaction::STATUS_REFUNDED) {
                return response()->json(['ok' => true, 'message' => 'Already refunded.']);
            }

            $payment->forceFill([
                'status' => PaymentTransaction::STATUS_CONFIRMED,
                'confirmed_at' => now(),
                'failed_at' => null,
                'last_error' => null,
            ])->save();

            IssueTicketForPayment::dispatch($payment->id);

            return response()->json(['ok' => true, 'message' => 'Payment confirmed.']);
        }

        if ($state === 'refunded') {
            $lifecycleService->markRefunded($payment->fresh(['ticket']), 'Payment refunded by provider.');

            return response()->json(['ok' => true, 'message' => 'Payment refunded.']);
        }

        if ($state === 'pending') {
            if ($payment->status === PaymentTransaction::STATUS_INITIATED) {
                $payment->forceFill([
                    'status' => PaymentTransaction::STATUS_PENDING,
                ])->save();
            }

            return response()->json(['ok' => true, 'message' => 'Payment still pending.']);
        }

        if ($payment->status === PaymentTransaction::STATUS_CONFIRMED) {
            return response()->json(['ok' => true, 'message' => 'Already confirmed; ignoring failed callback.']);
        }

        $lifecycleService->markFailedAndRelease($payment, (string) (data_get($payload, 'message') ?: 'Payment failed callback received.'));

        return response()->json(['ok' => true, 'message' => 'Payment marked failed.']);
    }
}
