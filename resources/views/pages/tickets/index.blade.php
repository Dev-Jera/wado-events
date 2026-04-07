@extends('layouts.app')

@section('content')
    <section class="tickets-page">
        <div class="tickets-shell">
            <div class="tickets-head">
                <div>
                    <p class="tickets-kicker">Customer account</p>
                    <h1>My Tickets</h1>
                </div>
            </div>

            <div class="tickets-grid">
                @forelse ($tickets as $ticket)
                    <article class="ticket-card">
                        <p class="ticket-code">{{ $ticket->ticket_code }}</p>
                        <h2>{{ $ticket->event->title }}</h2>
                        <p>{{ $ticket->event->starts_at->format('d M Y, h:i A') }}</p>
                        <p>{{ $ticket->ticketCategory->name }} · {{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</p>
                        <div class="ticket-meta">
                            <span>{{ $ticket->payment_provider === 'free' ? 'Free' : strtoupper((string) $ticket->payment_provider) }}</span>
                            <span>{{ (float) $ticket->total_amount <= 0 ? 'No charge' : 'UGX '.number_format((float) $ticket->total_amount, 0) }}</span>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}" class="ticket-link">Open ticket</a>
                    </article>
                @empty
                    <div class="ticket-empty">
                        <h2>No tickets yet</h2>
                        <p>Once you complete a booking, your tickets will show up here.</p>
                        <a href="{{ route('events.index') }}">Browse events</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        .tickets-page { min-height: 100vh; padding: 8.5rem 1rem 4rem; background: linear-gradient(180deg, #08111f 0%, #0b1627 100%); }
        .tickets-shell { width: min(1100px, calc(100% - 2rem)); margin: 0 auto; }
        .tickets-kicker { margin: 0; color: #f8b26a; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.13em; font-weight: 700; }
        .tickets-head h1 { margin: 0.5rem 0 0; font-size: clamp(2rem, 5vw, 3.4rem); }
        .tickets-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-top: 1.4rem; }
        .ticket-card, .ticket-empty { border-radius: 26px; padding: 1.4rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); }
        .ticket-code { margin: 0; color: #f8b26a; font-size: 0.8rem; letter-spacing: 0.12em; font-weight: 700; }
        .ticket-card h2 { margin: 0.85rem 0 0; font-size: 1.4rem; }
        .ticket-card p { color: #d7e1f2; line-height: 1.6; }
        .ticket-meta { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; font-weight: 700; }
        .ticket-link, .ticket-empty a { color: #f8b26a; text-decoration: none; font-weight: 700; }
        .ticket-empty { grid-column: 1 / -1; }
        @media (max-width: 920px) { .tickets-grid { grid-template-columns: 1fr; } }
    </style>
@endsection
