<?php

namespace App\Http\Controllers;

use App\Models\GateBatch;
use App\Services\GatePrint\GatePrintTicketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GateBatchController extends Controller
{
    public function __construct(
        protected GatePrintTicketService $printService
    ) {}

    public function downloadPdf(Request $request, int $batchId)
    {
        $user = $request->user();

        abort_unless(
            $user?->isSuperAdmin() || $user?->isAdmin() || $user?->isEventOwner(),
            403
        );

        $batch = GateBatch::with('event')->findOrFail($batchId);

        // If this is an event-owner, ensure the batch belongs to their event
        if ($user->isEventOwner() && ! $user->isSuperAdmin() && ! $user->isAdmin()) {
            abort_unless((int) $batch->event?->user_id === (int) $user->id, 403);
        }

        abort_if($batch->status === 'voided', 422, 'This batch has been voided and cannot be printed.');

        // Auto-generate tickets on first download if still in draft
        $this->printService->ensureGenerated($batch->fresh());
        $batch->refresh();

        $tickets = $batch->tickets()->orderBy('id')->get();

        // Verify each ticket's HMAC before rendering — silently drops any
        // record whose stored payload has been tampered with.
        $tickets = $tickets->filter(function ($ticket) {
            $decoded = json_decode((string) $ticket->qr_payload, true);
            return is_array($decoded) && $this->printService->verifyPayload($decoded);
        });

        abort_if($tickets->isEmpty(), 422, 'No valid tickets found for this batch.');

        // Attach QR data URIs — generated in-memory, not stored to disk
        $tickets = $tickets->map(function ($ticket) {
            $ticket->qr_data_uri = $this->printService->buildQrDataUri((string) $ticket->qr_payload);
            return $ticket;
        });

        $sizeVars = $this->sizeVariables($batch->ticket_size);

        $pdf = Pdf::loadView('pdf.gate-tickets', array_merge([
            'batch'   => $batch,
            'event'   => $batch->event,
            'tickets' => $tickets,
        ], $sizeVars));

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 150,
        ]);

        $slug     = str($batch->event?->title ?? 'event')->slug('-');
        $filename = "gate-tickets-batch{$batch->id}-{$slug}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Return CSS/layout variables for each ticket size.
     * All dimensions in mm for dompdf's CSS.
     */
    protected function sizeVariables(string $size): array
    {
        return match ($size) {
            'large' => [
                'ticketHeightMm'  => 70,
                'cellWidthPct'    => 100,
                'bodyWidthPct'    => 72,
                'gapMm'           => 5,
                'padMm'           => 4,
                'stripeHeightMm'  => 5,
                'qrSizeMm'        => 38,
                'fontLgMm'        => 5.5,
                'fontSmMm'        => 3.8,
                'fontXsMm'        => 3.2,
            ],
            'standard' => [
                'ticketHeightMm'  => 55,
                'cellWidthPct'    => 50,
                'bodyWidthPct'    => 70,
                'gapMm'           => 4,
                'padMm'           => 3.5,
                'stripeHeightMm'  => 4,
                'qrSizeMm'        => 30,
                'fontLgMm'        => 4.8,
                'fontSmMm'        => 3.4,
                'fontXsMm'        => 2.9,
            ],
            default => [ // small
                'ticketHeightMm'  => 42,
                'cellWidthPct'    => 25,
                'bodyWidthPct'    => 65,
                'gapMm'           => 3,
                'padMm'           => 2.5,
                'stripeHeightMm'  => 3,
                'qrSizeMm'        => 22,
                'fontLgMm'        => 4.0,
                'fontSmMm'        => 3.0,
                'fontXsMm'        => 2.5,
            ],
        };
    }
}
