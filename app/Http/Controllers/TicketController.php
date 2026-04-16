<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Services\Ticket\TicketQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    public function __construct(protected TicketQrService $ticketQrService)
    {
    }

    public function index()
    {
        $user = auth()->user();

        $tickets = $user
            ->tickets()
            ->with(['event', 'ticketCategory', 'paymentTransaction'])
            ->latest('purchased_at')
            ->get();

        $tickets->each(function (Ticket $ticket): void {
            $missingQrFile = $ticket->qr_code_path && ! Storage::disk('public')->exists($ticket->qr_code_path);

            if (! $ticket->qr_code_path || $missingQrFile) {
                $ticket->forceFill([
                    'qr_code_path' => $this->ticketQrService->generateAndStoreForTicket($ticket),
                ])->save();
            }

            $ticket->setAttribute('qr_code_url', $this->getQrCodePublicUrl($ticket));
        });

        // Bookmarked upcoming/live events the user doesn't have a ticket for yet
        $bookmarkedEvents = $user
            ->bookmarks()
            ->with('event.ticketCategories')
            ->get()
            ->map(fn ($b) => $b->event)
            ->filter(fn ($e) => $e && in_array($e->live_status, ['upcoming', 'live'], true))
            ->sortBy(fn ($e) => $e->starts_at)
            ->values();

        return view('pages.tickets.index', [
            'tickets' => $tickets,
            'bookmarkedEvents' => $bookmarkedEvents,
        ]);
    }

    public function requestRefund(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $payment = PaymentTransaction::query()
            ->where('ticket_id', $ticket->id)
            ->latest('id')
            ->first();

        if (! $payment) {
            return back()->with('error', 'Refund request failed: payment transaction not found for this ticket.');
        }

        if ($payment->status === PaymentTransaction::STATUS_REFUNDED || $ticket->status === Ticket::STATUS_CANCELLED) {
            return back()->with('error', 'This ticket is already refunded/cancelled.');
        }

        if ($ticket->used_at || $ticket->status === Ticket::STATUS_USED) {
            return back()->with('error', 'Used tickets are not eligible for refund requests.');
        }

        if (! in_array($payment->status, [
            PaymentTransaction::STATUS_CONFIRMED,
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_INITIATED,
        ], true)) {
            return back()->with('error', 'This payment status is not eligible for refund request.');
        }

        if ($payment->refund_requested_at && $payment->status !== PaymentTransaction::STATUS_REFUNDED) {
            return back()->with('info', 'Refund request already submitted for this ticket.');
        }

        $reason = trim((string) $data['reason']);

        $providerPayload = (array) ($payment->provider_payload ?? []);
        $providerPayload['customer_refund_request'] = [
            'requested_at' => now()->toIso8601String(),
            'reason' => $reason,
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
        ];

        $payment->forceFill([
            'refund_requested_at' => now(),
            'refund_request_status' => 'REQUESTED',
            'refund_request_reason' => $reason,
            'provider_payload' => $providerPayload,
        ])->save();

        return back()->with('success', 'Refund request sent. Our team will review it shortly.');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);

        $ticket->load(['event.category', 'ticketCategory']);
        $this->ensureTicketQrCode($ticket);

        $view = request()->query('modal') === '1'
            ? 'pages.tickets.modal'
            : 'pages.tickets.show';

        return view($view, [
            'ticket' => $ticket,
        ]);
    }

    public function download(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);
        $this->ensureTicketQrCode($ticket);

        $qrPath = Storage::disk('public')->path($ticket->qr_code_path);

        return response()->download($qrPath, sprintf('wado-ticket-qr-%s.svg', $ticket->ticket_code));
    }

    public function downloadPdf(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);

        $ticket->load(['event.category', 'ticketCategory']);
        $this->ensureTicketQrCode($ticket);

        $pdf = Pdf::loadView('pages.tickets.pdf_compact', [
            'ticket' => $ticket,
            'qrCodeDataUri' => $this->getQrCodeDataUri($ticket),
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Disable remote images to prevent hanging
            'defaultFont' => 'sans-serif',
            'isFontSubsettingEnabled' => true,
        ]);

        return $pdf->download(sprintf('wado-ticket-%s.pdf', $ticket->ticket_code));
    }

    protected function ensureTicketQrCode(Ticket $ticket): void
    {
        $ticket->forceFill([
            'qr_code_path' => $this->ticketQrService->generateAndStoreForTicket($ticket),
        ])->save();

        $ticket->setAttribute('qr_code_url', $this->getQrCodePublicUrl($ticket));
    }

    protected function getQrCodeDataUri(Ticket $ticket): ?string
    {
        if (! $ticket->qr_code_path || ! Storage::disk('public')->exists($ticket->qr_code_path)) {
            return null;
        }

        return 'data:image/svg+xml;base64,' . base64_encode(
            Storage::disk('public')->get($ticket->qr_code_path)
        );
    }

    protected function getQrCodePublicUrl(Ticket $ticket): ?string
    {
        if (! $ticket->qr_code_path) {
            return null;
        }

        $url = Storage::disk('public')->url($ticket->qr_code_path);
        $path = parse_url($url, PHP_URL_PATH);

        return $path ?: $url;
    }
}
