@extends('layouts.app')

@php
    $image = str_starts_with((string) $event->image_url, 'http') ? $event->image_url : asset(ltrim((string) $event->image_url, '/'));
@endphp

@section('content')
    <section class="event-show">
        <div class="event-show-shell">

            {{-- Hero --}}
            <div class="event-hero" style="background-image: linear-gradient(rgba(7,16,28,0.35), rgba(7,16,28,0.88)), url('{{ $image }}')">
                <p class="eyebrow">{{ $event->category?->name ?? 'Uncategorized' }}</p>
                <h1>{{ $event->title }}</h1>
                <p class="hero-desc">{{ $event->description }}</p>
                <div class="hero-actions">
                    <span class="pill-price">{{ (float) $event->ticket_price <= 0 ? 'Free Entry' : 'From UGX '.number_format((float) $event->ticket_price, 0) }}</span>
                    <span class="pill-status">{{ ucfirst($event->status) }}</span>
                    @if ($event->ticketCategories->isNotEmpty())
                        <a href="{{ route('checkout.create', $event) }}" class="btn-buy">
                            {{ (float) $event->ticket_price <= 0 ? 'Get Ticket' : 'Buy Ticket' }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Schedule / Location / Tickets --}}
            <div class="details-row">
                <div class="detail-card">
                    <p class="dc-label">Schedule</p>
                    <p class="dc-main">{{ $event->starts_at->format('l, d M Y') }}</p>
                    <p class="dc-sub">{{ $event->starts_at->format('h:i A') }}@if($event->ends_at) – {{ $event->ends_at->format('h:i A') }}@endif</p>
                </div>
                <div class="detail-card">
                    <p class="dc-label">Location</p>
                    <p class="dc-main">{{ $event->venue }}</p>
                    <p class="dc-sub">{{ $event->city }}, {{ $event->country }}</p>
                </div>
                <div class="detail-card">
                    <p class="dc-label">Tickets</p>
                    <p class="dc-main">{{ $event->tickets_available }} left</p>
                    <p class="dc-sub">Capacity: {{ $event->capacity }}</p>
                </div>
            </div>

            {{-- Ticket categories --}}
            <div class="section-card">
                <p class="section-title">Ticket categories</p>
                <div class="tickets-grid">
                    @forelse ($event->ticketCategories as $ticketCategory)
                        <div class="ticket-item">
                            <p class="ti-name">{{ $ticketCategory->name }}</p>
                            <p class="ti-desc">{{ $ticketCategory->description ?: 'Custom ticket option for this event.' }}</p>
                            <div class="ti-footer">
                                <span class="ti-price">{{ (float) $ticketCategory->price <= 0 ? 'Free' : 'UGX '.number_format((float) $ticketCategory->price, 0) }}</span>
                                <span class="ti-left">{{ $ticketCategory->tickets_remaining }} / {{ $ticketCategory->ticket_count }} left</span>
                                <a href="{{ route('checkout.create', [$event, 'ticket_category' => $ticketCategory->id]) }}" class="ti-btn">
                                    {{ (float) $ticketCategory->price <= 0 ? 'Get Ticket' : 'Buy now' }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="dc-sub">No ticket categories added yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Artists (only shown if present) --}}
            @if ($event->artists->isNotEmpty())
                <div class="section-card">
                    <p class="section-title">Artists &amp; Speakers</p>
                    <div class="artists-wrap">
                        @foreach ($event->artists as $artist)
                            <span class="artist-tag">{{ $artist->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    <style>
        /* ── Page ── */
        .event-show {
            padding: 9rem 1rem 4rem;
            background: linear-gradient(180deg, #150508 0%, #1e0b0e 100%);
            min-height: 100vh;
        }
        .event-show-shell {
            width: min(1000px, calc(100% - 2rem));
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* ── Hero ── */
        .event-hero {
            border-radius: 20px;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: 300px;
        }
        .eyebrow {
            font-size: 12px;
            color: #1a73e8;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .event-hero h1 {
            font-size: 1.9rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
            margin: 0 0 8px;
        }
        .hero-desc {
            font-size: 14px;
            color: #b0bfd4;
            line-height: 1.6;
            max-width: 560px;
            margin: 0 0 16px;
        }
        .hero-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .pill-price {
            background: #1a73e8;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 999px;
        }
        .pill-status {
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 16px;
            border-radius: 999px;
            border: 0.5px solid rgba(255,255,255,0.2);
        }
        .btn-buy {
            background: #e8241a;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 7px 20px;
            border-radius: 999px;
            text-decoration: none;
        }
        .btn-buy:hover { opacity: 0.9; }

        /* ── Detail cards ── */
        .details-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .detail-card {
            background: rgba(26,115,232,0.08);
            border: 0.5px solid rgba(26,115,232,0.2);
            border-radius: 14px;
            padding: 16px 18px;
        }
        .dc-label {
            font-size: 11px;
            color: #1a73e8;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .dc-main {
            font-size: 15px;
            font-weight: 500;
            color: #fff;
            margin-bottom: 2px;
        }
        .dc-sub {
            font-size: 13px;
            color: #8a9ab8;
        }

        /* ── Section cards (tickets, artists) ── */
        .section-card {
            background: rgba(26,115,232,0.06);
            border: 0.5px solid rgba(26,115,232,0.15);
            border-radius: 14px;
            padding: 18px 20px;
        }
        .section-title {
            font-size: 11px;
            color: #1a73e8;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 14px;
            font-weight: 600;
        }

        /* ── Ticket items ── */
        .tickets-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .ticket-item {
            background: rgba(255,255,255,0.04);
            border: 0.5px solid rgba(26,115,232,0.15);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .ti-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }
        .ti-desc {
            font-size: 12px;
            color: #8a9ab8;
            line-height: 1.5;
        }
        .ti-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }
        .ti-price {
            font-size: 14px;
            font-weight: 700;
            color: #1a73e8;
        }
        .ti-left {
            font-size: 12px;
            color: #6b7ea0;
        }
        .ti-btn {
            background: #e8241a;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 999px;
            text-decoration: none;
            white-space: nowrap;
        }
        .ti-btn:hover { background: #c01e15; }

        /* ── Artists ── */
        .artists-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .artist-tag {
            background: rgba(26,115,232,0.12);
            color: #7ab3f5;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 999px;
            border: 0.5px solid rgba(26,115,232,0.3);
        }

        /* ── Responsive ── */
        @media (max-width: 700px) {
            .details-row,
            .tickets-grid { grid-template-columns: 1fr; }
            .event-hero h1 { font-size: 1.5rem; }
        }
    </style>
@endsection