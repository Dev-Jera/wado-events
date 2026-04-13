<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\Ticket\TicketQrService;
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
            ->with(['event', 'ticketCategory'])
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
