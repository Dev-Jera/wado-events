<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\Ticket\TicketQrService;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(protected TicketQrService $ticketQrService)
    {
    }

    public function index()
    {
        $tickets = auth()->user()
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

            $ticket->setAttribute('qr_code_url', Storage::disk('public')->url($ticket->qr_code_path));
        });

        return view('pages.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);

        $ticket->load(['event.category', 'ticketCategory']);

        $missingQrFile = $ticket->qr_code_path && ! Storage::disk('public')->exists($ticket->qr_code_path);

        if (! $ticket->qr_code_path || $missingQrFile) {
            $ticket->forceFill([
                'qr_code_path' => $this->ticketQrService->generateAndStoreForTicket($ticket),
            ])->save();
        }

        $ticket->setAttribute('qr_code_url', Storage::disk('public')->url($ticket->qr_code_path));

        return view('pages.tickets.show', [
            'ticket' => $ticket,
        ]);
    }
}
