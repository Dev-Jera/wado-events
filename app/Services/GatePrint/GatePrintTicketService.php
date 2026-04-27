<?php

namespace App\Services\GatePrint;

use App\Models\GateBatch;
use App\Models\GateTicket;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GatePrintTicketService
{
    /**
     * Generate tickets for a batch if they don't exist yet, then mark it active.
     */
    public function ensureGenerated(GateBatch $batch): void
    {
        if ($batch->tickets()->exists()) {
            return;
        }

        $this->generateTickets($batch);
    }

    /**
     * Generate all ticket records for a batch and mark it active.
     * Wrapped in a transaction; re-checks status under lock to prevent
     * double-generation if two requests arrive simultaneously.
     */
    public function generateTickets(GateBatch $batch): void
    {
        DB::transaction(function () use ($batch): void {
            // Re-read status with a row lock so concurrent requests serialise
            $locked = GateBatch::query()
                ->lockForUpdate()
                ->findOrFail($batch->id);

            if ($locked->status !== 'draft') {
                // Already generated (or voided) by a concurrent request — bail silently
                return;
            }

            $rows = [];
            $now  = now()->toDateTimeString();

            for ($i = 0; $i < $locked->quantity; $i++) {
                $code    = $this->generateUniqueCode($locked->event_id);
                $payload = $this->buildSignedPayload($code, $locked->id, $locked->event_id);

                $rows[] = [
                    'batch_id'    => $locked->id,
                    'event_id'    => $locked->event_id,
                    'ticket_code' => $code,
                    'qr_payload'  => json_encode($payload, JSON_UNESCAPED_SLASHES),
                    'status'      => 'unprinted',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Insert in chunks to stay within DB max_allowed_packet
            collect($rows)->chunk(500)->each(
                fn ($chunk) => GateTicket::insert($chunk->values()->all())
            );

            $locked->update([
                'status'     => 'active',
                'printed_at' => now(),
            ]);
        });

        $batch->refresh();
    }

    /**
     * Build a base64 PNG data URI for embedding a QR code in a PDF.
     */
    public function buildQrDataUri(string $qrPayload): string
    {
        // Use SVG writer (doesn't need GD extension)
        $result = (new Builder(
            writer: new SvgWriter(),
            data: $qrPayload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 4,
        ))->build();

        return 'data:image/svg+xml;base64,' . base64_encode($result->getString());
    }

    /**
     * Verify a scanned gate-print QR payload's HMAC signature.
     */
    public function verifyPayload(array $payload): bool
    {
        $provided = (string) ($payload['sig'] ?? '');

        if ($provided === '') {
            return false;
        }

        return hash_equals($this->signPayload($payload), $provided);
    }

    // ── Internals ──────────────────────────────────────────────────────

    protected function generateUniqueCode(int $eventId): string
    {
        do {
            $suffix = Str::upper(Str::random(8));
            $code   = "GP-{$eventId}-{$suffix}";
        } while (GateTicket::where('ticket_code', $code)->exists());

        return $code;
    }

    protected function buildSignedPayload(string $code, int $batchId, int $eventId): array
    {
        $payload = [
            'v'        => 1,
            'type'     => 'gate_print',
            'code'     => $code,
            'batch_id' => $batchId,
            'event_id' => $eventId,
        ];

        $payload['sig'] = $this->signPayload($payload);

        return $payload;
    }

    protected function signPayload(array $payload): string
    {
        $base = implode('|', [
            'gate_print',
            strtoupper((string) ($payload['code'] ?? '')),
            (string) ($payload['event_id'] ?? ''),
            (string) ($payload['batch_id'] ?? ''),
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