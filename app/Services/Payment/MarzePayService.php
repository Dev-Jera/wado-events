<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MarzePayService
{
    public function initiateStkPush(PaymentTransaction $payment): array
    {
        $baseUrl = rtrim((string) config('services.marzepay.base_url', ''), '/');
        $path = (string) config('services.marzepay.stk_path', '/payments/stk-push');

        if ($baseUrl === '') {
            return [
                'ok' => false,
                'message' => 'MARZEPAY_BASE_URL is not configured.',
                'payload' => null,
                'reference' => null,
                'provider_status' => null,
            ];
        }

        $callbackUrl = rtrim((string) config('app.url'), '/')
            . '/'
            . ltrim((string) config('services.marzepay.callback_path', '/payments/marzepay/webhook'), '/');

        $payload = [
            'amount' => (float) $payment->total_amount,
            'currency' => (string) $payment->currency,
            'provider' => (string) $payment->payment_provider,
            'phone' => (string) $payment->phone_number,
            'reference' => (string) $payment->idempotency_key,
            'idempotency_key' => (string) $payment->idempotency_key,
            'callback_url' => $callbackUrl,
            'description' => 'WADO Ticket Payment',
            'metadata' => [
                'payment_transaction_id' => $payment->id,
                'event_id' => $payment->event_id,
                'ticket_category_id' => $payment->ticket_category_id,
                'quantity' => $payment->quantity,
            ],
        ];

        /** @var Response $response */
        $response = Http::timeout((int) config('services.marzepay.timeout', 30))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'X-API-KEY' => (string) config('services.marzepay.api_key', ''),
                'X-API-SECRET' => (string) config('services.marzepay.api_secret', ''),
            ])
            ->post($baseUrl . $path, $payload);

        $json = $response->json() ?: [];
        $ok = $response->successful() && $this->normalizeProviderState($json) !== 'failed';

        return [
            'ok' => $ok,
            'message' => (string) data_get($json, 'message', $response->reason() ?: 'Unable to initiate STK push.'),
            'payload' => $json,
            'reference' => (string) (data_get($json, 'transaction_id')
                ?? data_get($json, 'reference')
                ?? data_get($json, 'data.transaction_id')
                ?? data_get($json, 'data.reference')
                ?? ''),
            'provider_status' => $this->normalizeProviderState($json),
        ];
    }

    public function verifyWebhookSignature(string $rawBody, ?string $providedSignature): bool
    {
        $secret = (string) config('services.marzepay.webhook_secret', '');

        if ($secret === '' || blank($providedSignature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, trim((string) $providedSignature));
    }

    public function resolveWebhookState(array $payload): string
    {
        $status = strtolower((string) (
            data_get($payload, 'status')
            ?? data_get($payload, 'payment_status')
            ?? data_get($payload, 'event')
            ?? data_get($payload, 'data.status')
            ?? ''
        ));

        if (in_array($status, ['success', 'successful', 'paid', 'confirmed', 'complete', 'completed'], true)) {
            return 'confirmed';
        }

        if (in_array($status, ['refunded', 'refund', 'reversed', 'reversal'], true)) {
            return 'refunded';
        }

        if (in_array($status, ['pending', 'processing', 'initiated'], true)) {
            return 'pending';
        }

        return 'failed';
    }

    public function normalizeProviderState(array $payload): string
    {
        $status = strtolower((string) (data_get($payload, 'status') ?? data_get($payload, 'data.status') ?? 'pending'));

        if (in_array($status, ['success', 'successful', 'confirmed', 'pending'], true)) {
            return $status;
        }

        return 'failed';
    }
}
