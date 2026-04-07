<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()
            ->tickets()
            ->with(['event', 'ticketCategory'])
            ->latest('purchased_at')
            ->get();

        return view('pages.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);

        $ticket->load(['event.category', 'ticketCategory']);

        return view('pages.tickets.show', [
            'ticket' => $ticket,
        ]);
    }
}
