@extends('layouts.app')

@section('content')
    <section class="ticket-view-page">
        <div class="ticket-view-shell">
            <article class="ticket-view-card">
                <p class="ticket-view-kicker">Direct Ticket View</p>
                <h1>{{ $ticket->event->title }}</h1>
                <div class="ticket-view-grid">
                    <div>
                        <span class="ticket-view-label">Ticket code</span>
                        <strong>{{ $ticket->ticket_code }}</strong>
                    </div>
                    <div>
                        <span class="ticket-view-label">Category</span>
                        <strong>{{ $ticket->ticketCategory->name }}</strong>
                    </div>
                    <div>
                        <span class="ticket-view-label">Quantity</span>
                        <strong>{{ $ticket->quantity }}</strong>
                    </div>
                    <div>
                        <span class="ticket-view-label">Purchased</span>
                        <strong>{{ $ticket->purchased_at->format('d M Y, h:i A') }}</strong>
                    </div>
                    <div>
                        <span class="ticket-view-label">Venue</span>
                        <strong>{{ $ticket->event->venue }}, {{ $ticket->event->city }}</strong>
                    </div>
                    <div>
                        <span class="ticket-view-label">Amount</span>
                        <strong>{{ (float) $ticket->total_amount <= 0 ? 'Free' : 'UGX '.number_format((float) $ticket->total_amount, 0) }}</strong>
                    </div>
                </div>
                <div class="ticket-view-actions">
                    <a href="{{ route('tickets.index') }}">Back to My Tickets</a>
                    <a href="{{ route('events.show', $ticket->event) }}">View event</a>
                </div>
            </article>
        </div>
    </section>

    <style>
        .ticket-view-page { min-height: 100vh; padding: 8.5rem 1rem 4rem; background: radial-gradient(circle at top, #18253a 0%, #08111f 48%, #05080f 100%); }
        .ticket-view-shell { width: min(920px, calc(100% - 2rem)); margin: 0 auto; }
        .ticket-view-card { border-radius: 30px; padding: 2rem; background: linear-gradient(180deg, rgba(255,255,255,0.1), rgba(255,255,255,0.06)); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 28px 70px rgba(0,0,0,0.32); }
        .ticket-view-kicker { margin: 0; color: #f8b26a; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.14em; font-weight: 700; }
        .ticket-view-card h1 { margin: 0.7rem 0 0; font-size: clamp(2rem, 4vw, 3.4rem); }
        .ticket-view-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .ticket-view-grid > div { padding: 1rem; border-radius: 20px; background: rgba(255,255,255,0.06); }
        .ticket-view-label { display: block; color: #f8b26a; font-size: 0.78rem; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.4rem; }
        .ticket-view-actions { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem; }
        .ticket-view-actions a { color: #f8b26a; text-decoration: none; font-weight: 700; }
        @media (max-width: 760px) { .ticket-view-grid { grid-template-columns: 1fr; } }
    </style>
@endsection
