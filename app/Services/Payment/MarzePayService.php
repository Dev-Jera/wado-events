<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarzePayService
{
    public function initiateStkPush(PaymentTransaction $payment): array
    {
        $baseUrl = rtrim((string) config('services.marzepay.base_url', ''), '/');
        $apiKey = (string) config('services.marzepay.api_key', '');
        $apiSecret = (string) config('services.marzepay.api_secret', '');

        if ($baseUrl === '' || $apiKey === '' || $apiSecret === '') {
            Log::error('MarzePayService: Missing MarzPay configuration', [
                'base_url_empty' => $baseUrl === '',
                'api_key_empty' => $apiKey === '',
                'api_secret_empty' => $apiSecret === '',
            ]);
            return [
                'ok' => false,
                'message' => 'MarzPay is not properly configured.',
                'payload' => null,
                'reference' => null,
                'provider_status' => null,
            ];
        }

        $callbackUrl = rtrim((string) config('app.url'), '/')
            . '/'
            . ltrim((string) config('services.marzepay.callback_path', 'payments/marzepay/webhook'), '/');

        // Generate UUID v4 for reference if needed (idempotency_key should already be UUID)
        $reference = (string) $payment->idempotency_key;

        // Validate amount (500 - 10,000,000 UGX)
        $amount = (int) $payment->total_amount;
        if ($amount < 500 || $amount > 10000000) {
            Log::error('MarzPay: Invalid amount', [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'min' => 500,
                'max' => 10000000,
            ]);
            return [
                'ok' => false,
                'message' => "Invalid amount. MarzPay requires 500 - 10,000,000 UGX. Got: $amount",
                'payload' => null,
                'reference' => null,
                'provider_status' => null,
            ];
        }

        $phoneNumber = (string) $payment->phone_number;

        // Normalize to +256XXXXXXXXX while handling 256XXXXXXXXX and formatted input.
        $digits = preg_replace('/\D+/', '', $phoneNumber) ?? '';
        if ($digits === '') {
            return [
                'ok' => false,
                'message' => 'Invalid phone number provided.',
                'payload' => null,
                'reference' => null,
                'provider_status' => null,
            ];
        }

        if (str_starts_with($digits, '256')) {
            $phoneNumber = '+' . $digits;
        } else {
            $phoneNumber = '+256' . ltrim($digits, '0');
        }

        $payload = [
            'amount' => $amount,
            'phone_number' => $phoneNumber,
            'country' => (string) config('services.marzepay.country', 'UG'),
            'reference' => $reference,
            'description' => 'WADO Ticket Payment',
            'callback_url' => $callbackUrl,
        ];

        try {
            // Create Basic Auth header: base64(api_key:api_secret)
            $credentials = base64_encode($apiKey . ':' . $apiSecret);
            
            Log::debug('MarzPay request payload', [
                'payment_id' => $payment->id,
                'amount' => $payload['amount'],
                'phone_number' => $payload['phone_number'],
                'country' => $payload['country'],
                'reference' => $payload['reference'],
            ]);
            
            /** @var Response $response */
            $response = Http::timeout((int) config('services.marzepay.timeout', 30))
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => 'Basic ' . $credentials,
                ])
                ->asForm()
                ->post($baseUrl . '/collect-money', $payload);

            Log::info('MarzPay collection initiated', [
                'payment_id' => $payment->id,
                'status_code' => $response->status(),
                'reference' => $reference,
                'response_body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('MarzPay API call failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'ok' => false,
                'message' => 'Failed to connect to MarzPay API: ' . $e->getMessage(),
                'payload' => null,
                'reference' => null,
                'provider_status' => null,
            ];
        }

        $json = $response->json() ?: [];
        $status = (string) data_get($json, 'status', 'failed');
        $transactionStatus = (string) data_get($json, 'data.transaction.status', 'failed');
        $isSandboxMode = (bool) data_get($json, 'data.metadata.sandbox_mode', false);
        
        // API returns status "success" for both sandbox and live modes
        // Transaction status can be "pending" (live) or "sandbox" (sandbox mode)
        $ok = $response->successful() && $status === 'success';

        Log::info('MarzPay collection result', [
            'payment_id' => $payment->id,
            'ok' => $ok,
            'status' => $status,
            'transaction_status' => $transactionStatus,
            'sandbox_mode' => $isSandboxMode,
            'reference' => $reference,
        ]);

        return [
            'ok' => $ok,
            'message' => (string) data_get($json, 'message', $response->reason() ?: 'Unable to initiate collection.'),
            'payload' => $json,
            'reference' => (string) (data_get($json, 'data.transaction.uuid')
                ?? data_get($json, 'data.transaction.reference')
                ?? data_get($json, 'reference')
                ?? ''),
            'provider_status' => $transactionStatus === 'sandbox' ? 'sandbox' : ($ok ? 'pending' : 'failed'),
        ];
    }

    public function verifyWebhookSignature(string $rawBody, ?string $providedSignature): bool
    {
        $secret = (string) config('services.marzepay.webhook_secret', '');

        // MarzPay does not send webhook signatures — allow through when no secret is configured
        if ($secret === '') {
            Log::warning('MarzPay webhook received with no secret configured — accepting without verification');
            return true;
        }

        if (blank($providedSignature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, trim((string) $providedSignature));
    }

    public function resolveWebhookState(array $payload): string
    {
        // MarzPay sends event_type: "collection.completed" / "collection.failed"
        $eventType = strtolower((string) (data_get($payload, 'event_type') ?? ''));
        if (str_ends_with($eventType, '.completed')) {
            return 'confirmed';
        }
        if (str_ends_with($eventType, '.failed') || str_ends_with($eventType, '.cancelled')) {
            return 'failed';
        }

        $status = strtolower((string) (
            data_get($payload, 'transaction.status')
            ?? data_get($payload, 'status')
            ?? data_get($payload, 'payment_status')
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

    public function requestRefund(PaymentTransaction $payment, string $reason): array
    {
        $baseUrl = rtrim((string) config('services.marzepay.base_url', ''), '/');
        $apiKey = (string) config('services.marzepay.api_key', '');
        $apiSecret = (string) config('services.marzepay.api_secret', '');
        $refundPath = '/' . ltrim((string) config('services.marzepay.refund_path', 'refund-money'), '/');

        if ($baseUrl === '' || $apiKey === '' || $apiSecret === '') {
            return [
                'ok' => false,
                'message' => 'MarzPay is not properly configured for refunds.',
                'payload' => null,
                'provider_status' => null,
            ];
        }

        $reference = (string) ($payment->provider_reference ?: $payment->idempotency_key);
        $payload = [
            'reference' => $reference,
            'transaction_reference' => $reference,
            'provider_reference' => $reference,
            'amount' => (int) $payment->total_amount,
            'currency' => (string) ($payment->currency ?: 'UGX'),
            'reason' => $reason,
        ];

        if (! blank($payment->phone_number)) {
            $payload['phone_number'] = (string) $payment->phone_number;
        }

        try {
            $credentials = base64_encode($apiKey . ':' . $apiSecret);

            /** @var Response $response */
            $response = Http::timeout((int) config('services.marzepay.timeout', 30))
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => 'Basic ' . $credentials,
                ])
                ->asForm()
                ->post($baseUrl . $refundPath, $payload);
        } catch (\Throwable $exception) {
            Log::error('MarzPay refund API call failed', [
                'payment_id' => $payment->id,
                'error' => $exception->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => 'Failed to connect to MarzPay refund API: ' . $exception->getMessage(),
                'payload' => null,
                'provider_status' => null,
            ];
        }

        $json = $response->json() ?: [];
        $status = strtolower((string) (data_get($json, 'status') ?? data_get($json, 'data.status') ?? ''));
        $transactionStatus = strtolower((string) (
            data_get($json, 'data.transaction.status')
            ?? data_get($json, 'data.refund.status')
            ?? data_get($json, 'refund.status')
            ?? ''
        ));

        $ok = $response->successful()
            && (
                in_array($status, ['success', 'successful', 'ok', 'accepted', 'processing', 'pending', 'refunded', 'refund', 'reversed', 'reversal'], true)
                || in_array($transactionStatus, ['accepted', 'processing', 'pending', 'refunded', 'refund', 'reversed', 'reversal'], true)
            );

        $providerStatus = $transactionStatus !== '' ? $transactionStatus : $status;

        Log::info('MarzPay refund result', [
            'payment_id' => $payment->id,
            'ok' => $ok,
            'status' => $status,
            'transaction_status' => $transactionStatus,
            'response_code' => $response->status(),
            'endpoint' => $baseUrl . $refundPath,
        ]);

        return [
            'ok' => $ok,
            'message' => (string) (data_get($json, 'message') ?: $response->reason() ?: ($ok ? 'Refund request accepted.' : 'MarzPay rejected the refund request.')),
            'payload' => $json,
            'provider_status' => $providerStatus !== '' ? $providerStatus : null,
        ];
    }
}
