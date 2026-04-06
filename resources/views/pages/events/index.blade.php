@extends('layouts.app')

@section('content')
    <section class="events-page">
        <div class="events-shell">
            <div class="events-head">
                <div>
                    <p class="eyebrow">Public listings</p>
                    <h1>Events</h1>
                </div>
                <a href="{{ route('admin.events.create') }}" class="head-action">Add a new event</a>
            </div>

            <div class="events-grid">
                @forelse ($events as $event)
                    <article class="event-card">
                        <div class="event-image" style="background-image: url('{{ asset(ltrim((string) $event->image_url, '/')) }}')"></div>
                        <div class="event-body">
                            <div class="event-topline">
                                <span class="badge">{{ ucfirst($event->status) }}</span>
                                <span>{{ $event->category_label ?? ($event->category?->name ?? 'Uncategorized') }}</span>
                            </div>
                            <h2>{{ $event->title }}</h2>
                            <p>{{ \Illuminate\Support\Str::limit($event->description, 140) }}</p>
                            <ul class="event-meta">
                                <li>{{ $event->starts_at->format('d M Y, h:i A') }}</li>
                                <li>{{ $event->venue }}, {{ $event->city }}</li>
                                <li>{{ $event->tickets_available }} / {{ $event->capacity }} tickets left</li>
                            </ul>
                            <div class="event-footer">
                                <strong>From UGX {{ number_format((float) $event->ticket_price, 2) }}</strong>
                                <a href="{{ $event->url ?? route('events.show', $event) }}">View Event</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <h2>No events available</h2>
                        <p>Use the admin dashboard to create your first event listing.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        .events-page { padding: 9rem 1rem 4rem; background: linear-gradient(180deg, #07101c 0%, #0b1627 100%); }
        .events-shell { width: min(1120px, calc(100% - 2rem)); margin: 0 auto; }
        .events-head { display: flex; justify-content: space-between; align-items: end; gap: 1rem; margin-bottom: 1.5rem; }
        .events-head h1 { margin: 0.4rem 0 0; font-size: clamp(2rem, 4vw, 3.4rem); }
        .head-action { color: #09111c; background: #f8b26a; padding: 0.85rem 1.15rem; border-radius: 999px; text-decoration: none; font-weight: 700; }
        .events-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .event-card, .empty-state { overflow: hidden; border-radius: 26px; background: #f8fafc; color: #101828; }
        .event-image { min-height: 220px; background-size: cover; background-position: center; background-color: #d8dee8; }
        .event-body { padding: 1.3rem; }
        .event-topline, .event-meta, .event-footer { display: flex; justify-content: space-between; gap: 0.8rem; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 0.28rem 0.72rem; border-radius: 999px; background: #fee2e2; color: #b91c1c; font-size: 0.8rem; font-weight: 700; }
        .event-body h2 { margin: 0.8rem 0 0; font-size: 1.35rem; }
        .event-body p, .event-meta { color: #475467; line-height: 1.65; }
        .event-meta { list-style: none; padding: 0; margin: 1rem 0 0; }
        .event-footer { margin-top: 1rem; align-items: center; }
        .event-footer a { color: #b45309; text-decoration: none; font-weight: 700; }
        .empty-state { grid-column: 1 / -1; padding: 2rem; }
        @media (max-width: 980px) { .events-grid { grid-template-columns: 1fr; } .events-head { align-items: start; flex-direction: column; } }
    </style>
@endsection
