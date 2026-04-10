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
            data: json_encode($payload, JSON_UNESCAPED_SLASHES),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 600,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        $legacyPngPath = 'qr-codes/ticket-' . $ticket->id . '.png';
        if (Storage::disk('public')->exists($legacyPngPath)) {
            Storage::disk('public')->delete($legacyPngPath);
        }

        $path = 'qr-codes/ticket-' . $ticket->id . '.svg';
        Storage::disk('public')->put($path, $result->getString());

        return $path;
    }

    public function buildSignedPayload(Ticket $ticket): array
    {
        $ticket->loadMissing(['user', 'event']);

        $payload = [
            'v' => 2,
            'code' => (string) $ticket->ticket_code,
            'event_id' => (int) $ticket->event_id,
        ];

        $payload['sig'] = $this->signPayload($payload);

        return $payload;
    }

    public function parsePayload(string $raw): ?array
    {
        $decoded = json_decode(trim($raw), true);
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
        $appKey = (string) config('app.key', '');

        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);

            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $appKey;
    }
}
