@extends('layouts.app')

@section('content')
    <section class="events-page">
        <div class="events-shell">
            <div class="events-head">
                <div>
                    <p class="eyebrow">Public listings</p>
                    <h1>Discover Events</h1>
                </div>
              
            </div>

            <div class="events-list">
                @forelse ($events as $event)
                    <article class="event-row">
                        <div class="event-banner" style="background-image: url('{{ asset(ltrim((string) $event->image_url, '/')) }}')"></div>
                        <div class="event-body">
                            <h2>{{ $event->title }}</h2>
                            <div class="event-bottom">
                                <ul class="event-details">
                                    <li class="detail-item">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        {{ $event->starts_at->format('d M Y, h:i A') }}
                                    </li>
                                    <li class="detail-item">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                                        {{ $event->venue }}{{ $event->city ? ', ' . $event->city : '' }}
                                    </li>
                                </ul>
                                <div class="event-actions">
                                    <span class="event-price">{{ (float) $event->ticket_price <= 0 ? 'Free' : 'From UGX ' . number_format((float) $event->ticket_price, 0) }}</span>
                                    <a href="{{ $event->url ?? route('events.show', $event) }}" class="btn-view">View details</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <h2>No events yet</h2>
                        <p>Use the admin dashboard to create your first event listing.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        /* ── Page shell ── */
        .events-page {
            padding: 9rem 1rem 4rem;
            background: linear-gradient(180deg, #07101c 0%, #0b1627 100%);
            min-height: 100vh;
        }
        .events-shell {
            width: min(1120px, calc(100% - 2rem));
            margin: 0 auto;
        }

        /* ── Header ── */
        .events-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .eyebrow {
            color: #6b7280;
            font-size: 13px;
            margin: 0 0 4px;
        }
        .events-head h1 {
            margin: 0;
            font-size: clamp(1.4rem, 2.5vw, 2rem);
            color: #fff;
        }
        .head-action {
            color: #1a0e00;
            background: #f8b26a;
            padding: 0.75rem 1.25rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .head-action:hover { background: #e8961a; }

        /* ── Event list ── */
        .events-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* ── Event row ── */
        .event-row {
            display: grid;
            grid-template-columns: 220px 1fr;
            background: #1a1a1a;
            border-radius: 16px;
            overflow: hidden;
            border: 0.5px solid #2a2a2a;
            min-height: 148px;
            transition: border-color 0.15s;
        }
        .event-row:hover { border-color: #444; }

        /* ── Banner ── */
        .event-banner {
            background-size: cover;
            background-position: center;
            background-color: #1f2d3d;
            flex-shrink: 0;
        }

        /* ── Body ── */
        .event-body {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 10px;
        }

        .event-body h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }

        /* ── Bottom section ── */
        .event-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .event-details {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #aaa;
            font-size: 13px;
        }
        .detail-item svg { opacity: 0.6; flex-shrink: 0; }

        /* ── Actions ── */
        .event-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .event-price {
            font-size: 14px;
            color: #f2b674;
            font-weight: 600;
            margin-right: 6px;
        }
        .btn-view {
            padding: 7px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-view {
            color: #1a0e00;
            border: 0.5px solid #f8b26a;
            background: #f8b26a;
        }
        .btn-view:hover { background: #e8a250; border-color: #e8a250; }

        /* ── Empty state ── */
        .empty-state {
            background: #1a1a1a;
            border: 0.5px solid #2a2a2a;
            border-radius: 16px;
            padding: 3rem 2rem;
            text-align: center;
            color: #888;
        }
        .empty-state h2 { color: #fff; margin: 0 0 0.5rem; font-weight: 500; }

        /* ── Responsive ── */
        @media (max-width: 700px) {
            .event-row { grid-template-columns: 1fr; }
            .event-banner { min-height: 160px; }
            .events-head { align-items: flex-start; flex-direction: column; }
            .event-bottom { flex-direction: column; align-items: flex-start; }
            .event-actions { width: 100%; justify-content: flex-end; }
        }
    </style>
@endsection