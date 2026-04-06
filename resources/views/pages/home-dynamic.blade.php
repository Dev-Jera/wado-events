@extends('layouts.app')

@php
    $heroEvent = $featuredEvents->first();
@endphp

@section('content')
    <section class="home-hero">
        <div class="hero-shell">
            <div>
                <p class="eyebrow">Wado Events</p>
                <h1>Discover events that are ready for the spotlight.</h1>
                <p class="hero-copy">
                    Dashboard events show here automatically. If there are no events in the database yet, the site falls back to the sample showcase so the frontend still looks complete.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('events.index') }}" class="btn btn-primary">Browse Events</a>
                    <a href="{{ url('/dashboard/events/create') }}" class="btn btn-secondary">Create Event</a>
                </div>
            </div>

            @if ($heroEvent)
                <article class="hero-card">
                    <span class="hero-card-tag">{{ $heroEvent->category_label }}</span>
                    <h2>{{ $heroEvent->title }}</h2>
                    <p>{{ \Illuminate\Support\Str::limit($heroEvent->description, 130) }}</p>
                    <ul class="hero-meta">
                        <li>{{ $heroEvent->starts_at->format('d M Y, h:i A') }}</li>
                        <li>{{ $heroEvent->venue }}, {{ $heroEvent->city }}</li>
                        <li>From UGX {{ number_format((float) $heroEvent->ticket_price, 2) }}</li>
                    </ul>
                    <a href="{{ $heroEvent->url }}" class="hero-link">View event</a>
                </article>
            @endif
        </div>
    </section>

    <section class="featured-events">
        <div class="featured-shell">
            <div class="section-head">
                <div>
                    <p class="eyebrow">Current listings</p>
                    <h2>Featured events</h2>
                </div>
                <a href="{{ route('events.index') }}" class="section-link">View all events</a>
            </div>

            <div class="featured-grid">
                @foreach ($featuredEvents as $event)
                    <article class="event-card">
                        <div class="event-image" style="background-image: url('{{ asset(ltrim((string) $event->image_url, '/')) }}')"></div>
                        <div class="event-body">
                            <div class="event-topline">
                                <span class="event-badge">{{ $event->category_label }}</span>
                                <span>{{ ucfirst($event->status) }}</span>
                            </div>
                            <h3>{{ $event->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($event->description, 110) }}</p>
                            <div class="event-meta">
                                <span>{{ $event->starts_at->format('d M Y') }}</span>
                                <span>{{ $event->city }}, {{ $event->country }}</span>
                            </div>
                            <div class="event-footer">
                                <strong>From UGX {{ number_format((float) $event->ticket_price, 2) }}</strong>
                                <a href="{{ $event->url }}">Open</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <style>
        .home-hero { padding: 9rem 1rem 3rem; background: linear-gradient(135deg, #08111f 0%, #14243d 55%, #0c1728 100%); }
        .hero-shell, .featured-shell { width: min(1120px, calc(100% - 2rem)); margin: 0 auto; }
        .hero-shell { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1.2rem; align-items: stretch; }
        .eyebrow { margin: 0 0 0.8rem; color: #f8b26a; text-transform: uppercase; letter-spacing: 0.14em; font-size: 0.8rem; font-weight: 700; }
        .home-hero h1 { margin: 0; max-width: 12ch; font-size: clamp(2.3rem, 6vw, 4.8rem); line-height: 0.94; letter-spacing: -0.04em; }
        .hero-copy { max-width: 58ch; color: #d4dceb; font-size: 1.04rem; line-height: 1.7; margin: 1.2rem 0 0; }
        .hero-actions { display: flex; gap: 0.8rem; flex-wrap: wrap; margin-top: 1.5rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.9rem 1.2rem; border-radius: 999px; text-decoration: none; font-weight: 700; }
        .btn-primary { color: #fff; background: linear-gradient(135deg, #f15a24, #dc2626); }
        .btn-secondary { color: #fff; border: 1px solid rgba(255, 255, 255, 0.18); background: rgba(255, 255, 255, 0.08); }
        .hero-card, .event-card { border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(14px); border-radius: 28px; overflow: hidden; }
        .hero-card { padding: 1.5rem; }
        .hero-card-tag, .event-badge { display: inline-flex; padding: 0.35rem 0.7rem; border-radius: 999px; background: rgba(248, 178, 106, 0.16); color: #ffd8af; font-size: 0.78rem; font-weight: 700; }
        .hero-card h2 { margin: 0.9rem 0 0; font-size: 1.6rem; }
        .hero-card p, .hero-meta { color: #d7e1f2; line-height: 1.6; }
        .hero-meta { list-style: none; padding: 0; margin: 1rem 0 0; display: grid; gap: 0.6rem; }
        .hero-link, .section-link, .event-footer a { color: #ffcf9e; text-decoration: none; font-weight: 700; }
        .featured-events { padding: 3rem 1rem 4rem; background: linear-gradient(180deg, #07101c 0%, #0b1627 100%); }
        .section-head { display: flex; justify-content: space-between; align-items: end; gap: 1rem; margin-bottom: 1.4rem; }
        .section-head h2 { margin: 0.35rem 0 0; font-size: 2rem; }
        .featured-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .event-image { min-height: 220px; background-size: cover; background-position: center; background-color: #243140; }
        .event-body { padding: 1.2rem; }
        .event-topline, .event-meta, .event-footer { display: flex; justify-content: space-between; gap: 0.8rem; flex-wrap: wrap; }
        .event-body h3 { margin: 0.85rem 0 0; font-size: 1.3rem; }
        .event-body p, .event-meta { color: #d4dceb; line-height: 1.65; }
        .event-footer { margin-top: 1rem; align-items: center; }
        @media (max-width: 900px) { .hero-shell, .featured-grid { grid-template-columns: 1fr; } .section-head { flex-direction: column; align-items: start; } }
    </style>
@endsection
