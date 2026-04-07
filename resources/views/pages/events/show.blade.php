@extends('layouts.app')

@php
    $image = str_starts_with((string) $event->image_url, 'http') ? $event->image_url : asset(ltrim((string) $event->image_url, '/'));
@endphp

@section('content')
    <section class="event-show">
        <div class="event-show-shell">
            <div class="event-hero" style="background-image: linear-gradient(rgba(7, 16, 28, 0.55), rgba(7, 16, 28, 0.82)), url('{{ $image }}')">
                <div class="hero-panel">
                    <p class="eyebrow">{{ $event->category?->name ?? 'Uncategorized' }}</p>
                    <h1>{{ $event->title }}</h1>
                    <p>{{ $event->description }}</p>
                    <div class="hero-actions">
                        <span class="price-pill">{{ (float) $event->ticket_price <= 0 ? 'Free Entry' : 'From UGX '.number_format((float) $event->ticket_price, 2) }}</span>
                        <span class="status-pill">{{ ucfirst($event->status) }}</span>
                        @if ($event->ticketCategories->isNotEmpty())
                            <a href="{{ route('checkout.create', $event) }}" class="buy-ticket-btn">
                                {{ (float) $event->ticket_price <= 0 ? 'Reserve Spot' : 'Buy Ticket' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="details-grid">
                <article class="detail-card">
                    <h2>Schedule</h2>
                    <p>{{ $event->starts_at->format('l, d M Y') }}</p>
                    <p>{{ $event->starts_at->format('h:i A') }}@if($event->ends_at) - {{ $event->ends_at->format('h:i A') }}@endif</p>
                </article>
                <article class="detail-card">
                    <h2>Location</h2>
                    <p>{{ $event->venue }}</p>
                    <p>{{ $event->city }}, {{ $event->country }}</p>
                </article>
                <article class="detail-card">
                    <h2>Tickets</h2>
                    <p>{{ $event->tickets_available }} left</p>
                    <p>Capacity: {{ $event->capacity }}</p>
                </article>
            </div>

            @if ($event->artists->isNotEmpty())
                <div class="artists-card">
                    <h2>Artists</h2>
                    <div class="artists-grid">
                        @foreach ($event->artists as $artist)
                            <span class="artist-pill">{{ $artist->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="ticket-options-card">
                <h2>Ticket categories</h2>
                <div class="ticket-options-grid">
                    @forelse ($event->ticketCategories as $ticketCategory)
                        <article class="ticket-option">
                            <strong>{{ $ticketCategory->name }}</strong>
                            <p>{{ $ticketCategory->description ?: 'Custom ticket option for this event.' }}</p>
                            <div class="ticket-option-meta">
                                <span>{{ (float) $ticketCategory->price <= 0 ? 'Free' : 'UGX '.number_format((float) $ticketCategory->price, 2) }}</span>
                                <span>{{ $ticketCategory->tickets_remaining }} / {{ $ticketCategory->ticket_count }} left</span>
                            </div>
                            <a href="{{ route('checkout.create', [$event, 'ticket_category' => $ticketCategory->id]) }}" class="ticket-option-btn">
                                {{ (float) $ticketCategory->price <= 0 ? 'Reserve now' : 'Buy now' }}
                            </a>
                        </article>
                    @empty
                        <p>No ticket categories added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <style>
        .event-show { padding: 9rem 1rem 4rem; background: linear-gradient(180deg, #07101c 0%, #0b1627 100%); }
        .event-show-shell { width: min(1100px, calc(100% - 2rem)); margin: 0 auto; }
        .event-hero { min-height: 420px; border-radius: 30px; overflow: hidden; background-size: cover; background-position: center; display: flex; align-items: end; padding: 2rem; }
        .hero-panel { width: min(620px, 100%); }
        .event-hero h1 { margin: 0; font-size: clamp(2.2rem, 5vw, 4rem); line-height: 0.98; }
        .event-hero p { color: #d7e1f2; line-height: 1.7; }
        .hero-actions { display: flex; gap: 0.8rem; flex-wrap: wrap; margin-top: 1rem; }
        .price-pill, .status-pill { display: inline-flex; padding: 0.72rem 1rem; border-radius: 999px; font-weight: 700; }
        .price-pill { color: #09111c; background: #f8b26a; }
        .status-pill { background: rgba(255, 255, 255, 0.14); }
        .buy-ticket-btn, .ticket-option-btn { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border-radius: 999px; font-weight: 700; }
        .buy-ticket-btn { color: #fff; background: linear-gradient(90deg, #ef4444, #b91c1c); padding: 0.72rem 1rem; }
        .details-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-top: 1.2rem; }
        .detail-card { background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; padding: 1.4rem; }
        .detail-card h2 { margin-top: 0; }
        .detail-card p { color: #d7e1f2; line-height: 1.6; }
        .artists-card { margin-top: 1.2rem; background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; padding: 1.4rem; }
        .artists-card h2 { margin-top: 0; }
        .artists-grid { display: flex; flex-wrap: wrap; gap: 0.7rem; }
        .artist-pill { display: inline-flex; align-items: center; padding: 0.65rem 0.95rem; border-radius: 999px; background: rgba(248, 178, 106, 0.16); color: #ffe0bc; font-weight: 700; }
        .ticket-options-card { margin-top: 1.2rem; background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; padding: 1.4rem; }
        .ticket-options-card h2 { margin-top: 0; }
        .ticket-options-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .ticket-option { padding: 1rem; border-radius: 18px; background: rgba(255, 255, 255, 0.05); }
        .ticket-option p { color: #d7e1f2; line-height: 1.6; }
        .ticket-option-meta { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; font-weight: 700; }
        .ticket-option-btn { margin-top: 0.85rem; color: #09111c; background: #f8b26a; padding: 0.7rem 0.95rem; }
        @media (max-width: 900px) { .details-grid, .ticket-options-grid { grid-template-columns: 1fr; } }
    </style>
@endsection
