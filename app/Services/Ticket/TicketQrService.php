<?php

namespace App\Services\Ticket;

use App\Models\Ticket;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketQrService
{
    public function generateUniqueTicketCode(int $userId, int $eventId): string
    {
        do {
            $suffix = Str::upper(Str::random(6));
            $code = sprintf('WADO-%d-%d-%s', $userId, $eventId, $suffix);
        } while (Ticket::query()->where('ticket_code', $code)->exists());

        return $code;
    }

    public function generateAndStoreForTicket(Ticket $ticket): string
    {
        $payload = $this->buildSignedPayload($ticket);

        $result = (new Builder(
            writer: new SvgWriter(),
            writerOptions: [],
            data: $this->encryptPayload($payload),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 600,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        $legacyPngPath = 'qr-codes/ticket-' . $ticket->id . '.png';
        if (Storage::disk('public')->exists($legacyPngPath)) {
            Storage::disk('public')->delete($legacyPngPath);
        }

        $legacySvgPath = 'qr-codes/ticket-' . $ticket->id . '.svg';
        if (Storage::disk('public')->exists($legacySvgPath)) {
            Storage::disk('public')->delete($legacySvgPath);
        }

        $path = 'qr-codes/' . Str::lower((string) $ticket->ticket_code) . '.svg';
        Storage::disk('public')->put($path, $result->getString());

        return $path;
    }

    public function buildSignedPayload(Ticket $ticket): array
    {
        $payload = [
            'v' => 2,
            'code' => (string) $ticket->ticket_code,
            'event_id' => (int) $ticket->event_id,
        ];

        $payload['sig'] = $this->signPayload($payload);

        return $payload;
    }

    public function encryptPayload(array $payload): string
    {
        $json  = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $key   = $this->deriveEncryptionKey();
        $nonce = random_bytes(12);
        $tag   = '';

        $ciphertext = openssl_encrypt($json, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag);

        if ($ciphertext === false) {
            throw new \RuntimeException('QR payload encryption failed.');
        }

        return 'WADOv3:' . base64_encode($nonce . $tag . $ciphertext);
    }

    protected function decryptPayload(string $raw): ?array
    {
        try {
            $binary = base64_decode(substr($raw, 7), true); // strip 'WADOv3:'
            if ($binary === false || strlen($binary) < 29) {
                return null;
            }

            $nonce      = substr($binary, 0, 12);
            $tag        = substr($binary, 12, 16);
            $ciphertext = substr($binary, 28);
            $key        = $this->deriveEncryptionKey();

            $json = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag);
            if ($json === false) {
                return null; // tampered or wrong key
            }

            $decoded = json_decode($json, true);
            if (! is_array($decoded) || ! Arr::has($decoded, ['code', 'sig'])) {
                return null;
            }

            return $decoded;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function deriveEncryptionKey(): string
    {
        // Derive a 32-byte AES key from the signing secret using a different context
        // so the encryption key is always distinct from the HMAC signing key
        return hash_hmac('sha256', 'wado-qr-aes-v3', $this->resolveSigningSecret(), true);
    }

    public function parsePayload(string $raw): ?array
    {
        $raw = trim($raw);

        // Encrypted v3 format — new tickets
        if (str_starts_with($raw, 'WADOv3:')) {
            return $this->decryptPayload($raw);
        }

        // Legacy plain-JSON format — tickets issued before encryption was added
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return null;
        }

        if (! Arr::has($decoded, ['code', 'sig'])) {
            return null;
        }

        return $decoded;
    }

    public function verifyPayloadSignature(array $payload): bool
    {
        $provided = (string) ($payload['sig'] ?? '');
        if ($provided === '') {
            return false;
        }

        $expected = $this->signPayload($payload);

        return hash_equals($expected, $provided);
    }

    protected function signPayload(array $payload): string
    {
        $version = (int) ($payload['v'] ?? 1);

        $base = $version >= 2
            ? implode('|', [
                'v2',
                strtoupper((string) ($payload['code'] ?? '')),
                (string) ($payload['event_id'] ?? ''),
            ])
            : implode('|', [
                (string) ($payload['v'] ?? ''),
                strtoupper((string) ($payload['code'] ?? '')),
                (string) ($payload['name'] ?? ''),
                (string) ($payload['event_id'] ?? ''),
                (string) ($payload['event'] ?? ''),
                (string) ($payload['purchased_at'] ?? ''),
            ]);

        return hash_hmac('sha256', $base, $this->resolveSigningSecret());
    }

    protected function resolveSigningSecret(): string
    {
        $ticketSigningKey = (string) config('app.ticket_signing_key', '');
        $secret = $ticketSigningKey !== '' ? $ticketSigningKey : (string) config('app.key', '');

        if (str_starts_with($secret, 'base64:')) {
            $decoded = base64_decode(substr($secret, 7), true);

            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $secret;
    }
}
