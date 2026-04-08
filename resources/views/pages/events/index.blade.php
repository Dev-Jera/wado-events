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
                            <div class="event-top">
                                <div class="event-meta-left">
                                    <div class="event-badges">
                                        <span class="badge badge-{{ strtolower($event->status) }}">{{ ucfirst($event->status) }}</span>
                                        <span class="badge badge-category">{{ $event->category_label ?? ($event->category?->name ?? 'Uncategorized') }}</span>
                                    </div>
                                    <h2>{{ $event->title }}</h2>
                                    <p class="event-desc">{{ $event->description }}</p>
                                </div>
                            </div>
                            <div class="event-bottom">
                                <ul class="event-details">
                                    <li class="detail-item">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        {{ $event->starts_at->format('d M Y, h:i A') }}
                                    </li>
                                    <li class="detail-item">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                                        {{ $event->venue }}, {{ $event->city }}
                                    </li>
                                    <li class="tickets-pill">
                                        <span class="tickets-count">{{ $event->tickets_available }}</span> / {{ $event->capacity }} tickets left
                                    </li>
                                </ul>
                                <div class="event-actions">
                                    <span class="event-price">From UGX {{ number_format((float) $event->ticket_price, 0) }}</span>
                                    @can('update', $event)
                                        <a href="{{ route('dashboard.events.edit', $event) }}" class="btn-edit">Edit</a>
                                    @endcan
                                    <a href="{{ $event->url ?? route('events.show', $event) }}" class="btn-view">View event ↗</a>
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
            gap: 12px;
        }

        /* ── Top section ── */
        .event-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }
        .badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
            display: inline-flex;
        }
        .badge-published  { background: #ff6b6b22; color: #ff8080; border: 0.5px solid #ff6b6b44; }
        .badge-draft      { background: #88888822; color: #aaa;    border: 0.5px solid #88888844; }
        .badge-cancelled  { background: #f59e0b22; color: #f59e0b; border: 0.5px solid #f59e0b44; }
        .badge-category   { background: transparent; color: #888;  border: 0.5px solid #333; }

        .event-body h2 {
            margin: 0 0 4px;
            font-size: 17px;
            font-weight: 500;
            color: #fff;
        }
        .event-desc {
            margin: 0;
            font-size: 13px;
            color: #888;
            line-height: 1.55;
            max-width: 560px;
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
            gap: 20px;
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
        .tickets-pill {
            font-size: 12px;
            color: #888;
            background: #252525;
            border-radius: 999px;
            padding: 4px 12px;
        }
        .tickets-count { color: #f8b26a; }

        /* ── Actions ── */
        .event-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .event-price {
            font-size: 13px;
            color: #888;
            margin-right: 4px;
        }
        .btn-edit, .btn-view {
            padding: 7px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-edit {
            color: #aaa;
            border: 0.5px solid #333;
            background: transparent;
        }
        .btn-edit:hover { border-color: #555; color: #ccc; }
        .btn-view {
            color: #f8b26a;
            border: 0.5px solid #f8b26a;
            background: transparent;
        }
        .btn-view:hover { background: #f8b26a11; }

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