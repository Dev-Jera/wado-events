<style>
.hp-cats {
    display: flex; flex-wrap: wrap;
    justify-content: center;
    gap: .45rem;
    margin-top: 1.6rem;
}
.hp-cat {
    display: inline-flex; align-items: center; gap: .38rem;
    padding: .42rem .95rem;
    border-radius: 999px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: var(--glass-blur-sm);
    -webkit-backdrop-filter: var(--glass-blur-sm);
    color: rgba(220,232,255,.82);
    font-size: .8rem; font-weight: 600;
    font-family: var(--site-font); cursor: pointer;
    transition: background .18s, border-color .18s, color .18s, box-shadow .18s;
}
.hp-cat:hover {
    background: var(--maroon-glass);
    border-color: rgba(160,32,46,.5);
    color: #fff;
}
.hp-cat.is-active {
    background: var(--maroon);
    border-color: var(--maroon);
    color: #fff;
    box-shadow: 0 4px 16px var(--maroon-glow);
}
.hp-cat-icon {
    width: .88rem; height: .88rem;
    stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    flex-shrink: 0;
}
.hp-cat-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 1.3rem; height: 1.3rem;
    border-radius: 999px;
    background: rgba(255,255,255,.14);
    font-size: .63rem; font-weight: 700;
    padding: 0 .3rem;
}
.hp-cat.is-active .hp-cat-count { background: rgba(255,255,255,.28); }
</style>
    <style>
    /* hero category chips: hidden on mobile — sticky bar takes over */
    .hp-cats { display: none; }
    .hp-cat { flex-shrink: 0; font-size: .76rem; padding: .36rem .72rem; }
    .hp-cat-count { display: none; }
    </style>
@extends('layouts.app')

@php
    $homeEvents  = $featuredEvents->take(12);
    $featuredRow = $homeEvents->take(6);

    $categoryPills = collect($categoryPills ?? []);
    if ($categoryPills->isEmpty()) {
        $categoryPills = $homeEvents
            ->groupBy(fn ($e) => \Illuminate\Support\Str::lower($e->category_label ?? 'uncategorized'))
            ->map(fn ($events) => [
                'label' => $events->first()->category_label,
                'count' => $events->count(),
            ]);
    }

    $iconMap = [
        'music'      => 'icon-music',
        'sports'     => 'icon-sports',
        'theater'    => 'icon-theater',
        'conference' => 'icon-briefcase',
        'workshop'   => 'icon-tools',
        'community'  => 'icon-community',
        'comedy'     => 'icon-comedy',
        'film'       => 'icon-film',
        'cinema'     => 'icon-film',
        'social'     => 'icon-community',
        'free event' => 'icon-free',
        'free'       => 'icon-free',
    ];

    // $heroImages, $packageSlides, $heroTitle, $heroSubtitle are passed by HomeController
@endphp

@section('content')

{{-- SVG sprite --}}
<svg class="icon-sprite" aria-hidden="true" focusable="false">
    <symbol id="icon-search"    viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4.5 4.5" stroke-linecap="round"/></symbol>
    <symbol id="icon-location"  viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></symbol>
    <symbol id="icon-clock"     viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2" stroke-linecap="round"/></symbol>
    <symbol id="icon-arrow-r"   viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="icon-music"     viewBox="0 0 24 24"><path d="M9 18a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm11-3a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zM9 18V6l11-2v11"/></symbol>
    <symbol id="icon-sports"    viewBox="0 0 24 24"><path d="M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9zm-5.7 9a11 11 0 0 1 2-5.6M17.7 12a11 11 0 0 1-2 5.6M8.9 6.4l2.2 2.2m1.8 0 2.2-2.2m-4 6.8-2.2 2.2m3.1 1.3h0m1.8-3.5 2.2 2.2"/></symbol>
    <symbol id="icon-theater"   viewBox="0 0 24 24"><path d="M4 5l4 2 4-2 4 2 4-2v11a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4zm5 7h.01M15 7h.01M9 14c1 .8 2 1.2 3 1.2s2-.4 3-1.2"/></symbol>
    <symbol id="icon-briefcase" viewBox="0 0 24 24"><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-11 3h16v8a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3zm0 0v-8m16 8v-8M10 12h4"/></symbol>
    <symbol id="icon-tools"     viewBox="0 0 24 24"><path d="M14.5 6.5a4 4 0 0 0-5.6 5.6L3 18v3h3l5.9-5.9a4 4 0 0 0 5.6-5.6l-2 2h-2v-2z"/></symbol>
    <symbol id="icon-community" viewBox="0 0 24 24"><path d="M16 11a3 3 0 1 0-3-3 3 3 0 0 0 3 3zM8 12a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm8 1c-2.8 0-5 1.2-5 3v2h10v-2c0-1.8-2.2-3-5-3zM8 14c-2.8 0-5 1.2-5 3v1h7v-1c0-1 .4-1.9 1.1-2.6A7.6 7.6 0 0 0 8 14z"/></symbol>
    <symbol id="icon-comedy"    viewBox="0 0 24 24"><path d="M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9zm-3 7h.01M15 10h.01M8 14c1.2 1.3 2.5 2 4 2s2.8-.7 4-2"/></symbol>
    <symbol id="icon-film"      viewBox="0 0 24 24"><path d="M3 6h18v12H3zm4 0v12M17 6v12M3 10h4m-4 4h4m10-4h4m-4 4h4"/></symbol>
    <symbol id="icon-free"      viewBox="0 0 24 24"><path d="M12 2l2.3 4.7 5.2.8-3.7 3.6.9 5.1L12 14l-4.7 2.2.9-5.1L4.5 7.5l5.2-.8L12 2z"/></symbol>
</svg>

{{-- ─────────────────────── HERO ─────────────────────── --}}
<section class="hp-hero" id="hp-hero">

    <div class="hp-hero-slide hp-hero-slide-intro is-active"
         style="background-image:url('{{ $heroImages[0] }}')"></div>
    <div class="hp-hero-slide"
         style="background-image:url('{{ $heroImages[1] }}')"></div>
    <div class="hp-hero-slide"
         style="background-image:url('{{ $heroImages[2] }}')"></div>
    <div class="hp-hero-veil"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.hp-hero-slide');
            let currentIndex = 0;

            setInterval(() => {
                slides[currentIndex].classList.remove('is-active');
                currentIndex = (currentIndex + 1) % slides.length;
                slides[currentIndex].classList.add('is-active');
            }, 5000); // Change slide every 5 seconds
        });
    </script>

    <div class="hp-hero-body hp-hero-panel hp-hero-panel-intro is-active">
        <p class="hp-hero-eyebrow">WADO EVENTS</p>
        <h1 class="hp-hero-heading">{!! nl2br(e($heroTitle)) !!}</h1>
        <p class="hp-hero-sub">{{ $heroSubtitle }}</p>

        {{-- glass search bar --}}
        <form class="hp-search-bar" id="hp-search-form" role="search"
              action="{{ route('events.index') }}" method="GET">
            <svg class="hp-search-icon" aria-hidden="true"><use href="#icon-search"/></svg>
            <input class="hp-search-input" id="hp-search-input" type="search" name="search"
                   placeholder="Search events, artists, venues…"
                   autocomplete="off" aria-label="Search events">
            <button class="hp-search-btn" type="submit">Search</button>
        </form>

        {{-- category chips --}}
        <div class="hp-cats" role="list" aria-label="Filter by category">
            <button class="hp-cat is-active" data-category="all" aria-pressed="true">
                <svg class="hp-cat-icon" aria-hidden="true"><use href="#icon-community"/></svg>
                All
            </button>
            @foreach ($categoryPills as $key => $cat)
                <button class="hp-cat" data-category="{{ $key }}" aria-pressed="false">
                    <svg class="hp-cat-icon" aria-hidden="true"><use href="#{{ $iconMap[$key] ?? 'icon-community' }}"/></svg>
                    {{ $cat['label'] }}
                    <span class="hp-cat-count">{{ $cat['count'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="hp-hero-body hp-hero-panel hp-hero-panel-packages" aria-live="polite">
        <div class="hp-packages-heading">
            <p class="hp-hero-eyebrow">FOR EVENT ORGANISERS</p>
            <h1 class="hp-hero-heading">Hosting an event?<br>Discover our ticket packages.</h1>
        </div>

        <div class="hp-package-stage">
            <div class="hp-package-track" id="hp-package-track">
                @foreach ($packageSlides as $i => $package)
                    <article class="hp-package-card {{ $i === 0 ? 'is-active' : 'hp-package-card--alt' }}">
                        <div class="hp-package-media">
                            @if(!empty($package['image']))
                                <img src="{{ $package['image'] }}" alt="{{ $package['label'] }}">
                            @endif
                        </div>
                        <div class="hp-package-copy">
                            <p class="hp-package-label">{{ $package['label'] }}</p>
                            <h2 class="hp-package-title">{{ $package['title'] }}</h2>
                            <p class="hp-package-text">{{ $package['copy'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="hp-package-dots" id="hp-package-dots" aria-label="Ticket package slides">
                @foreach ($packageSlides as $i => $package)
                    <button
                        type="button"
                        class="hp-package-dot {{ $i === 0 ? 'is-active' : '' }}"
                        data-package-index="{{ $i }}"
                        aria-label="Show {{ $package['label'] }}"
                        aria-pressed="{{ $i === 0 ? 'true' : 'false' }}"
                    ></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────── STICKY CATEGORY BAR ─────────────────────── --}}
<div class="hp-cat-bar" id="hp-cat-bar" role="navigation" aria-label="Filter by category">
    <div class="hp-cat-bar-inner">
        <button class="hp-cat is-active" data-category="all" aria-pressed="true" type="button">
            <svg class="hp-cat-icon" aria-hidden="true"><use href="#icon-community"/></svg>
            All
        </button>
        @foreach ($categoryPills as $key => $cat)
            <button class="hp-cat" data-category="{{ $key }}" aria-pressed="false" type="button">
                <svg class="hp-cat-icon" aria-hidden="true"><use href="#{{ $iconMap[$key] ?? 'icon-community' }}"/></svg>
                {{ $cat['label'] }}
                <span class="hp-cat-count">{{ $cat['count'] }}</span>
            </button>
        @endforeach
    </div>
</div>

{{-- Ticket Packages Marquee Section --}}
<section class="ticket-packages-marquee" style="margin-top: 0;">
    <div class="marquee-shell">
    <div class="marquee">
        <div class="marquee-content">
            @foreach ($packageSlides as $package)
                <div class="marquee-item">
                    @if(!empty($package['image']))<img src="{{ $package['image'] }}" alt="{{ $package['label'] }}" style="width: 100%; height: auto; object-fit: cover;">@endif
                    <div class="marquee-text">
                        <h3>{{ $package['title'] }}</h3>
                        <p>{{ $package['copy'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>{{-- /.marquee-shell --}}
</section>

{{-- ─────────────────────── FEATURED STRIP ─────────────────────── --}}
<section class="hp-featured" aria-label="Featured events">
    <div class="hp-shell">
        <div class="hp-sec-head">
            <div>
                <p class="hp-sec-kicker">HAND-PICKED FOR YOU</p>
                <h2 class="hp-sec-title">Featured Events</h2>
            </div>
            <a href="{{ route('events.index') }}" class="hp-see-all">
                See all <svg aria-hidden="true"><use href="#icon-arrow-r"/></svg>
            </a>
        </div>

        <div class="hp-strip-wrap">
            <div class="hp-strip" id="hp-feat-track">
                @foreach ($featuredRow as $event)
                    @php $price = (float) $event->ticket_price; @endphp
                    <article class="hp-fcard"
                        data-category="{{ \Illuminate\Support\Str::lower($event->category_label ?? 'uncategorized') }}"
                        data-href="{{ $event->url }}"
                        data-title="{{ $event->title }}"
                        data-description="{{ e($event->description) }}"
                        data-artists='@json(collect($event->artists ?? [])->pluck("name")->values())'
                        data-location="{{ e($event->venue.', '.$event->city) }}"
                        data-time="{{ $event->starts_at->format('h:i A') }}"
                        data-price="{{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}"
                        data-date="{{ $event->starts_at->format('d M') }}"
                        data-image="{{ asset(ltrim((string)$event->image_url,'/')) }}">

                        <div class="hp-fcard-img"
                             style="background-image:url('{{ asset(ltrim((string)$event->image_url,'/')) }}')">
                            <div class="hp-fcard-gradient"></div>
                            <span class="hp-fcard-date-badge">
                                <span class="hp-fcard-day">{{ $event->starts_at->format('d') }}</span>
                                <span class="hp-fcard-mon">{{ $event->starts_at->format('M') }}</span>
                            </span>
                            <span class="hp-fcard-cat-badge">{{ $event->category_label }}</span>
                        </div>

                        <div class="hp-fcard-body">
                            <h3 class="hp-fcard-title">{{ $event->title }}</h3>
                            <p class="hp-fcard-meta">
                                <svg aria-hidden="true"><use href="#icon-location"/></svg>
                                {{ $event->venue }}, {{ $event->city }}
                            </p>
                            <p class="hp-fcard-meta">
                                <svg aria-hidden="true"><use href="#icon-clock"/></svg>
                                {{ $event->starts_at->format('h:i A') }}
                            </p>
                            <div class="hp-fcard-foot">
                                <span class="hp-fcard-price {{ $price <= 0 ? 'is-free' : '' }}">
                                    {{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}
                                </span>
                                <a href="{{ $event->url }}" class="hp-fcard-btn"
                                   onclick="event.stopPropagation()">Join Event</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <button class="hp-arrow hp-arrow-prev" id="hp-feat-prev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="hp-arrow hp-arrow-next" id="hp-feat-next" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        </div>
    </div>
</section>

{{-- ─────────────────────── UPCOMING GRID ─────────────────────── --}}
<section class="hp-all" aria-label="Upcoming events">
    <div class="hp-shell">
        <div class="hp-sec-head">
            <div>
                <p class="hp-sec-kicker">DON'T MISS OUT</p>
                <h2 class="hp-sec-title">Upcoming Events</h2>
            </div>
            <a href="{{ route('events.index') }}" class="hp-see-all">
                Browse all <svg aria-hidden="true"><use href="#icon-arrow-r"/></svg>
            </a>
        </div>

        <div class="hp-grid" id="hp-grid">
            @foreach ($homeEvents as $event)
                @php
                    $key   = \Illuminate\Support\Str::lower($event->category_label ?? 'uncategorized');
                    $price = (float) $event->ticket_price;
                @endphp
                <article class="hp-ecard"
                    data-category="{{ $key }}"
                    data-title-search="{{ \Illuminate\Support\Str::lower($event->title) }}"
                    data-description="{{ e($event->description) }}"
                    data-artists='@json(collect($event->artists ?? [])->pluck("name")->values())'
                    data-href="{{ $event->url }}"
                    data-location="{{ e($event->venue.', '.$event->city) }}"
                    data-time="{{ $event->starts_at->format('h:i A') }}"
                    data-price="{{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}"
                    data-date="{{ $event->starts_at->format('d M') }}"
                    data-image="{{ asset(ltrim((string)$event->image_url,'/')) }}">

                    <div class="hp-ecard-thumb"
                         style="background-image:url('{{ asset(ltrim((string)$event->image_url,'/')) }}')">
                        <span class="hp-ecard-date-badge">
                            <span class="hp-ecard-day">{{ $event->starts_at->format('d') }}</span>
                            <span class="hp-ecard-mon">{{ $event->starts_at->format('M') }}</span>
                        </span>
                    </div>

                    <div class="hp-ecard-body">
                        <span class="hp-ecard-cat">{{ $event->category_label }}</span>
                        <h3 class="hp-ecard-title">{{ $event->title }}</h3>
                        <p class="hp-ecard-meta">
                            <svg aria-hidden="true"><use href="#icon-location"/></svg>
                            {{ $event->venue }}, {{ $event->city }}
                        </p>
                        <p class="hp-ecard-meta">
                            <svg aria-hidden="true"><use href="#icon-clock"/></svg>
                            {{ $event->starts_at->format('h:i A') }}
                        </p>
                        <div class="hp-ecard-foot">
                            <span class="hp-ecard-price {{ $price <= 0 ? 'is-free' : '' }}">
                                {{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}
                            </span>
                            <a href="{{ $event->url }}" class="hp-ecard-btn"
                               onclick="event.stopPropagation()">Get Tickets</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="hp-empty" id="hp-grid-empty" hidden>
            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <circle cx="22" cy="22" r="14"/>
                <path d="M33 33l9 9M16 22h12M22 16v12" stroke-linecap="round"/>
            </svg>
            <p>No events found.</p>
            <span>Try a different search or category.</span>
        </div>

        <div class="hp-grid-footer">
            <a href="{{ route('events.index') }}" class="hp-browse-btn">Browse All Events</a>
        </div>
    </div>
</section>

{{-- ─────────────────────── EVENT MODAL ─────────────────────── --}}
<div class="event-modal" id="event-modal" aria-hidden="true">
    <div class="event-modal-backdrop" data-modal-close></div>
    <article class="event-modal-card" role="dialog" aria-modal="true" aria-labelledby="event-modal-title">
        <button class="event-modal-close" type="button" data-modal-close aria-label="Close">×</button>
        <div class="event-modal-image" id="event-modal-image"></div>
        <div class="event-modal-content">
            <p class="event-modal-category" id="event-modal-category">Category</p>
            <h3 class="event-modal-title" id="event-modal-title">Event Title</h3>
            <p class="event-modal-description" id="event-modal-description">Event details will appear here.</p>
            <div class="event-modal-meta-grid">
                <p class="event-modal-row"><strong>Date</strong><span id="event-modal-date">Upcoming</span></p>
                <p class="event-modal-row"><strong>Time</strong><span id="event-modal-time">TBA</span></p>
                <p class="event-modal-row"><strong>Location</strong><span id="event-modal-location">Venue details</span></p>
                <p class="event-modal-row"><strong>Price</strong><span id="event-modal-price">Check listing</span></p>
            </div>
            <div class="event-modal-section" id="event-modal-artists-section">
                <h4>Artists</h4>
                <div class="event-modal-artists" id="event-modal-artists"></div>
            </div>
            <div class="event-modal-actions">
                <button class="event-modal-buy" id="event-modal-buy" type="button">Get Ticket</button>
                <a class="event-modal-link" id="event-modal-link" href="/events">View details</a>
            </div>
        </div>
    </article>
</div>

{{-- ─────────────────────── STYLES ─────────────────────── --}}
<style>
/* ── Root ───────────────────────────────────────────────────────────── */
:root {
    --maroon:       #c0283c;
    --maroon-hover: #a01e2e;
    --maroon-glow:  rgba(192, 40, 60, .4);
    --maroon-glass: rgba(192, 40, 60, .2);
    --blue:         #1255c0;
    --blue-hover:   #0e3fa0;
    --blue-glow:    rgba(18, 85, 192, .4);
    --green:        #22c55e;
    --green-dk:     #16a34a;

    /* glass — more opaque so cards feel solid and bright */
    --glass-bg:      rgba(255, 255, 255, .10);
    --glass-bg-md:   rgba(255, 255, 255, .15);
    --glass-bg-hi:   rgba(255, 255, 255, .22);
    --glass-border:  rgba(255, 255, 255, .22);
    --glass-blur:    blur(20px);
    --glass-blur-sm: blur(12px);

    /* text */
    --txt:   #fff;
    --muted: rgba(255, 215, 220, .70);
}

html, body { margin: 0; padding: 0; }
.icon-sprite { position: absolute; width: 0; height: 0; overflow: hidden; }

/* ── Page background — lighter warm dark, minimal gradient ── */
body {
    background:
        radial-gradient(ellipse 60% 40% at 0% 0%,   rgba(192, 40, 60, .15) 0%, transparent 50%),
        radial-gradient(ellipse 50% 35% at 100% 90%, rgba(192, 40, 60, .10) 0%, transparent 50%),
        #2a1015;
    background-attachment: fixed;
}

/* ── HERO ────────────────────────────────────────────────────────────── */
.hp-hero {
    position: relative;
    height: 60vh; /* Reduce the height of the hero section */
    margin-top: -20px; /* Push the hero section up */
    min-height: 640px;
    overflow: hidden;
}
@keyframes hpHeroPan {
    from { background-position: center top; }
    to   { background-position: center 16%; }
}

/* background slides */
.hp-hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center top;
    opacity: 0;
    transform: scale(1.02);
    transition: opacity 1.1s ease, transform 1.1s ease;
    z-index: 0;
}
.hp-hero-slide.is-active {
    opacity: 1;
    transform: scale(1);
}
.hp-hero-slide-intro.is-active {
    animation: hpHeroPan 6s ease-in-out both;
}
/* slide 2: intentional dark gradient, no photo */
.hp-hero-slide-package {
    background: linear-gradient(160deg, #0d1225 0%, #140810 100%);
}

/* dark veil over slide 1 photo — fades away on slide 2 */
.hp-hero-veil {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: linear-gradient(
        160deg,
        rgba(6, 8, 15, .68) 0%,
        rgba(6, 8, 15, .54) 45%,
        rgba(6, 8, 15, .82) 100%
    );
    transition: opacity .9s ease;
}
.hp-hero.is-packages .hp-hero-veil { opacity: 0; }

/* hero panels — both always absolutely positioned, same space */
.hp-hero-body {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 9.5rem 1.5rem 3rem;
    text-align: center;
    margin-inline: auto;
}
.hp-hero-panel-intro  { max-width: 680px; }
.hp-hero-panel-packages {
    max-width: 1120px;
    justify-content: flex-start;
    overflow: hidden;
    padding: 8rem 1.5rem 1.5rem;
}
.hp-hero-panel {
    opacity: 0;
    transform: translateY(16px);
    pointer-events: none;
    transition: opacity .6s ease, transform .6s ease;
}
.hp-hero-panel.is-active {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.hp-hero-eyebrow {
    margin: 0 0 .7rem;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .22em;
    color: rgba(255, 180, 185, .9);
    text-transform: uppercase;
}
.hp-hero-heading {
    margin: 0 0 .9rem;
    font-size: clamp(1.7rem, 6vw, 3.4rem);
    line-height: 1.1;
    letter-spacing: -.025em;
    font-weight: 800;
    color: #fff;
}
.hp-hero-sub {
    margin: 0 0 2.2rem;
    font-size: clamp(.88rem, 1.9vw, 1.05rem);
    color: var(--muted);
    line-height: 1.65;
}
.hp-hero-sub-packages {
    max-width: 44rem;
    margin-inline: auto;
}

/* heading wrapper — collapses when not on first package */
.hp-packages-heading {
    overflow: hidden;
    max-height: 10rem;
    opacity: 1;
    transition: max-height .45s ease, opacity .35s ease, margin .45s ease;
    margin-bottom: .5rem;
}
.hp-hero:not(.is-first-package) .hp-packages-heading {
    max-height: 0;
    opacity: 0;
    margin-bottom: 0;
}

/* ── glass search bar ── */
.hp-search-bar {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .4rem .4rem .4rem 1.1rem;
    border-radius: 999px;
    background: var(--glass-bg-md);
    border: 1.5px solid var(--glass-border);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    box-shadow: 0 8px 32px rgba(0,0,0,.28), inset 0 1px 0 rgba(255,255,255,.08);
    transition: border-color .2s, box-shadow .2s;
}
.hp-search-bar:focus-within {
    border-color: var(--blue);
    box-shadow: 0 8px 32px rgba(0,0,0,.28), 0 0 0 3px var(--blue-glow), inset 0 1px 0 rgba(255,255,255,.08);
}
.hp-search-icon {
    width: 1.1rem; height: 1.1rem;
    stroke: rgba(255,255,255,.45); fill: none;
    stroke-width: 2; stroke-linecap: round;
    flex-shrink: 0;
}
.hp-search-input {
    flex: 1; min-width: 0;
    background: transparent; border: none; outline: none;
    color: #fff; font-size: .95rem; font-family: var(--site-font);
}
.hp-search-input::placeholder { color: rgba(255,255,255,.35); }
.hp-search-btn {
    flex-shrink: 0;
    padding: .6rem 1.5rem;
    border-radius: 999px; border: none;
    background: var(--blue);
    color: #fff; font-size: .88rem; font-weight: 700;
    font-family: var(--site-font); cursor: pointer;
    box-shadow: 0 4px 14px var(--blue-glow);
    transition: background .18s, box-shadow .18s;
}
.hp-search-btn:hover { background: var(--blue-hover); box-shadow: 0 6px 20px var(--blue-glow); }

/* ── category chips ── */

/* ── Category bar (below hero) ── */
.hp-cat-bar {
    position: sticky;
    top: 4.2rem;
    z-index: 30;
    background: rgba(30, 10, 14, .94);
    border-bottom: 1px solid rgba(255,255,255,.09);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}
.hp-cat-bar-inner {
    display: flex;
    align-items: center;
    gap: .45rem;
    overflow-x: auto;
    padding: .62rem 1rem;
    max-width: 1220px;
    margin: 0 auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.hp-cat-bar-inner::-webkit-scrollbar { display: none; }
.hp-cat-bar .hp-cat { flex-shrink: 0; }

.hp-package-stage {
    flex: 1;
    display: flex;
    flex-direction: column;
    width: 100%;
    min-height: 0;
    margin-top: 1rem;
}
.hp-package-track {
    flex: 1;
    position: relative;
    min-height: 260px;
}
.hp-package-card {
    position: absolute;
    inset: 0;
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(260px, .92fr);
    align-items: center;
    gap: 1.2rem;
    padding: 1.1rem;
    border-radius: 28px;
    background: rgba(14, 17, 31, .5);
    border: 1px solid rgba(255,255,255,.12);
    box-shadow: 0 24px 60px rgba(0,0,0,.26);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    opacity: 0;
    pointer-events: none;
    transition: opacity .4s ease;
}
.hp-package-card.is-active {
    opacity: 1;
    pointer-events: auto;
}
/* image slides in from the left */
.hp-package-media {
    min-height: 280px;
    border-radius: 22px;
    overflow: hidden;
    background: rgba(255,255,255,.06);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
    transform: translateX(-40px);
    opacity: 0;
}
.hp-package-card.is-active .hp-package-media {
    transform: translateX(0);
    opacity: 1;
    transition: transform .65s cubic-bezier(.22,.68,0,1.1), opacity .5s ease;
}
.hp-package-media img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
/* description slides in from the right */
.hp-package-copy {
    text-align: left;
    padding: .45rem .3rem;
    transform: translateX(40px);
    opacity: 0;
}
.hp-package-card.is-active .hp-package-copy {
    transform: translateX(0);
    opacity: 1;
    transition: transform .65s cubic-bezier(.22,.68,0,1.1) .07s, opacity .5s ease .07s;
}

/* alt cards (index > 0): image uses scale+fade instead of slide */
.hp-package-card--alt .hp-package-media {
    transform: scale(1.04);
    opacity: 0;
}
.hp-package-card--alt.is-active .hp-package-media {
    transform: scale(1);
    opacity: 1;
    transition: transform .75s ease, opacity .55s ease;
}

.hp-package-label {
    margin: 0 0 .7rem;
    color: rgba(255, 196, 201, .92);
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
}
.hp-package-title {
    margin: 0 0 .8rem;
    color: #fff;
    font-size: clamp(1.35rem, 3vw, 2.15rem);
    line-height: 1.12;
    font-weight: 800;
}
.hp-package-text {
    margin: 0;
    color: rgba(180,200,240,.58);
    font-size: .84rem;
    line-height: 1.6;
}
.hp-package-dots {
    display: flex;
    justify-content: center;
    gap: .55rem;
    margin-top: 1rem;
}
.hp-package-dot {
    width: 11px;
    height: 11px;
    border-radius: 999px;
    border: none;
    background: rgba(255,255,255,.28);
    cursor: pointer;
    transition: transform .18s ease, background .18s ease, opacity .18s ease;
}
.hp-package-dot:hover {
    opacity: .85;
}
.hp-package-dot.is-active {
    background: #fff;
    transform: scale(1.18);
}

/* ── Shared section scaffolding ────────────────────────────────────── */
.ticket-packages-marquee {
    padding: 0 0 1.1rem;
    background: #321318;
}

.ticket-packages-marquee .marquee-shell {
    max-width: var(--site-width, 1140px);
    margin: 0 auto;
    padding: 0 1rem;
    overflow: hidden;
}

.marquee {
    overflow: hidden;
    position: relative;
    min-height: 150px;
    mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
    -webkit-mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
}

.marquee-content {
    display: flex;
    align-items: stretch;
    gap: 1rem;
    width: max-content;
    padding-right: 1rem;
    will-change: transform;
}

.marquee-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 1.2rem;
    border-radius: 999px;
    background: rgba(160, 32, 46, 0.18);
    border: 1px solid rgba(160, 32, 46, 0.4);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
}

.marquee-label span {
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 900;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    white-space: nowrap;
}

.marquee-item {
    display: grid;
    grid-template-columns: 132px minmax(240px, 360px);
    align-items: center;
    gap: .95rem;
    min-height: 146px;
    padding: .85rem;
    border-radius: 20px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.14);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 16px 40px rgba(0,0,0,.22);
}

.marquee-item img {
    width: 132px;
    height: 132px;
    border-radius: 16px;
    object-fit: cover;
    display: block;
}

.marquee-text h3 {
    margin: 0 0 .35rem;
    color: #fff;
    font-size: 1rem;
    line-height: 1.2;
    font-weight: 800;
}

.marquee-text p {
    margin: 0;
    color: #ffffff;
    font-size: .8rem;
    line-height: 1.55;
}

.hp-featured,
.hp-all {
    padding: 3.5rem 1rem 4rem;
    position: relative;
}
/* sections — solid warm dark so they read clearly */
.hp-featured {
    background: #321318;
    border-top:    1px solid rgba(255,255,255,.08);
    border-bottom: 1px solid rgba(255,255,255,.08);
}
.hp-all {
    background: #2a1015;
}

.hp-shell { width: min(1220px, 100%); margin: 0 auto; }

.hp-sec-head {
    display: flex; align-items: flex-end;
    justify-content: space-between;
    gap: 1rem; margin-bottom: 1.5rem;
}
.hp-sec-kicker {
    margin: 0;
    font-size: .66rem; font-weight: 700;
    letter-spacing: .16em; color: rgba(255,180,185,.85);
}
.hp-sec-title {
    margin: .3rem 0 0;
    font-size: clamp(1.3rem, 2.4vw, 1.9rem);
    color: #fff; font-weight: 800; line-height: 1.1;
}
.hp-see-all {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .84rem; font-weight: 700;
    color: rgba(255,180,185,.9); text-decoration: none; white-space: nowrap;
    transition: opacity .18s;
}
.hp-see-all:hover { opacity: .7; }
.hp-see-all svg {
    width: 1rem; height: 1rem;
    stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* ── Featured strip ─────────────────────────────────────────────────── */
.hp-strip-wrap { position: relative; }
.hp-strip {
    display: flex; gap: 1.1rem;
    overflow-x: auto; scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    padding-bottom: .5rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,.1) transparent;
}
.hp-strip::-webkit-scrollbar { height: 4px; }
.hp-strip::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 999px; }

/* featured card — glass */
.hp-fcard {
    flex: 0 0 300px;
    scroll-snap-align: start;
    border-radius: 18px;
    overflow: hidden;
    cursor: pointer;
    background: rgba(255,255,255,.09);
    border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    box-shadow: 0 8px 32px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.12);
    transition: transform .22s ease, border-color .2s, box-shadow .22s;
}
.hp-fcard:hover {
    transform: translateY(-7px);
    border-color: rgba(160,32,46,.6);
    box-shadow: 0 22px 50px rgba(0,0,0,.35), 0 0 0 1px rgba(160,32,46,.3), 0 0 30px rgba(160,32,46,.12);
}
.hp-fcard-img {
    position: relative;
    width: 100%; aspect-ratio: 16/10;
    background-size: cover; background-position: center;
}
.hp-fcard-gradient {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(6,8,15,.92) 0%, rgba(6,8,15,.05) 55%);
}
.hp-fcard-date-badge {
    position: absolute; top: .75rem; right: .75rem; z-index: 1;
    background: #fff;
    border-radius: 10px; padding: .28rem .55rem;
    display: flex; flex-direction: column; align-items: center; gap: 1px; line-height: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,.35);
}
.hp-fcard-day { font-size: 1rem; font-weight: 800; color: var(--maroon); }
.hp-fcard-mon { font-size: .56rem; font-weight: 700; letter-spacing: .07em; color: #222; text-transform: uppercase; }
.hp-fcard-cat-badge {
    position: absolute; bottom: .7rem; left: .75rem; z-index: 1;
    font-size: .63rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: #fff;
    background: var(--maroon-glass);
    border: 1px solid rgba(160,32,46,.45);
    backdrop-filter: var(--glass-blur-sm);
    border-radius: 6px; padding: .22rem .52rem;
}
.hp-fcard-body {
    padding: .9rem 1rem 1rem;
    display: flex; flex-direction: column; gap: .26rem;
}
.hp-fcard-title {
    margin: 0 0 .12rem;
    font-size: .97rem; font-weight: 700; color: #fff; line-height: 1.25;
    display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden;
}
.hp-fcard-meta {
    display: flex; align-items: center; gap: .3rem; margin: 0;
    font-size: .75rem; color: var(--muted); line-height: 1.3;
}
.hp-fcard-meta svg { width: .78rem; height: .78rem; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; opacity: .65; }
.hp-fcard-foot {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: .45rem; padding-top: .7rem;
    border-top: 1px solid rgba(255,255,255,.09);
}
.hp-fcard-price { font-size: .88rem; font-weight: 700; color: #fff; }
.hp-fcard-price.is-free { color: var(--green); }
.hp-fcard-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: .38rem .9rem; border-radius: 999px;
    background: var(--blue); color: #fff;
    font-size: .76rem; font-weight: 700; text-decoration: none;
    box-shadow: 0 3px 12px var(--blue-glow);
    transition: background .18s, transform .15s;
}
.hp-fcard-btn:hover { background: var(--blue-hover); transform: scale(1.04); }

/* scroll arrows */
.hp-arrow {
    position: absolute; top: 50%; transform: translateY(-60%);
    width: 2.4rem; height: 2.4rem; border-radius: 999px;
    background: var(--glass-bg-md);
    border: 1px solid var(--glass-border);
    backdrop-filter: var(--glass-blur-sm);
    color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    z-index: 3; box-shadow: 0 6px 18px rgba(0,0,0,.35);
    transition: background .18s, border-color .18s;
}
.hp-arrow:hover { background: var(--blue); border-color: var(--blue); }
.hp-arrow svg { width: 1.1rem; height: 1.1rem; }
.hp-arrow-prev { left: -.8rem; }
.hp-arrow-next { right: -.8rem; }

/* ── Upcoming grid ───────────────────────────────────────────────────── */
.hp-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.1rem;
}

/* event card — glass */
.hp-ecard {
    background: rgba(255,255,255,.09);
    border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border-radius: 16px; overflow: hidden; cursor: pointer;
    display: flex; flex-direction: column;
    box-shadow: 0 6px 24px rgba(0,0,0,.2), inset 0 1px 0 rgba(255,255,255,.12);
    transition: transform .22s ease, border-color .2s, box-shadow .22s;
}
.hp-ecard:hover {
    transform: translateY(-6px);
    border-color: rgba(160,32,46,.55);
    box-shadow: 0 20px 40px rgba(0,0,0,.3), 0 0 0 1px rgba(160,32,46,.25), 0 0 24px rgba(160,32,46,.1);
}
.hp-ecard.is-hidden { display: none; }

.hp-ecard-thumb {
    width: 100%; aspect-ratio: 3/2;
    background-size: cover; background-position: center;
    position: relative; flex-shrink: 0;
}
.hp-ecard-date-badge {
    position: absolute; top: .65rem; right: .65rem;
    background: #fff;
    border-radius: 8px; padding: .26rem .52rem;
    display: flex; flex-direction: column; align-items: center; gap: 1px; line-height: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,.35);
}
.hp-ecard-day { font-size: .95rem; font-weight: 800; color: var(--maroon); }
.hp-ecard-mon { font-size: .53rem; font-weight: 700; letter-spacing: .07em; color: #222; text-transform: uppercase; }

.hp-ecard-body {
    padding: .85rem 1rem 1rem;
    display: flex; flex-direction: column; flex: 1; gap: .2rem;
}
.hp-ecard-cat {
    font-size: .66rem; font-weight: 700; letter-spacing: .11em;
    color: rgba(255,180,185,.9); text-transform: uppercase;
}
.hp-ecard-title {
    margin: 0 0 .28rem;
    font-size: .95rem; font-weight: 700; color: #fff; line-height: 1.25;
    display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden;
}
.hp-ecard-meta {
    display: flex; align-items: center; gap: .3rem; margin: 0;
    font-size: .75rem; color: var(--muted); line-height: 1.3;
}
.hp-ecard-meta svg { width: .78rem; height: .78rem; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; opacity: .65; }
.hp-ecard-foot {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: auto; padding-top: .72rem;
    border-top: 1px solid rgba(255,255,255,.08);
}
.hp-ecard-price { font-size: .88rem; font-weight: 700; color: #fff; }
.hp-ecard-price.is-free { color: var(--green); }
.hp-ecard-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: .38rem .9rem; border-radius: 999px;
    background: var(--blue); color: #fff;
    font-size: .76rem; font-weight: 700; text-decoration: none;
    box-shadow: 0 3px 10px var(--blue-glow);
    transition: background .18s;
}
.hp-ecard-btn:hover { background: var(--blue-hover); }

/* empty state */
.hp-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .75rem; padding: 4rem 1rem; text-align: center; color: var(--muted);
}
.hp-empty svg { width: 3rem; height: 3rem; opacity: .35; }
.hp-empty p { margin: 0; font-size: .98rem; font-weight: 600; color: rgba(220,232,255,.7); }
.hp-empty span { font-size: .84rem; opacity: .7; }

/* browse button */
.hp-grid-footer { text-align: center; margin-top: 2.4rem; }
.hp-browse-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: .72rem 2.4rem; border-radius: 999px;
    background: var(--glass-bg-md);
    border: 1.5px solid rgba(26,115,232,.5);
    backdrop-filter: var(--glass-blur-sm);
    color: rgba(160,200,255,.95); font-size: .9rem; font-weight: 700; text-decoration: none;
    box-shadow: 0 4px 14px rgba(0,0,0,.2);
    transition: background .2s, color .2s, box-shadow .2s;
}
.hp-browse-btn:hover {
    background: var(--blue); color: #fff;
    box-shadow: 0 6px 22px var(--blue-glow);
}

/* ── Modal ────────────────────────────────────────────────────────────── */
.event-modal {
    position: fixed; inset: 0; z-index: 120;
    display: grid; align-items: center; justify-items: center;
    padding: 1rem;
    opacity: 0; visibility: hidden; pointer-events: none;
    transition: opacity .28s ease, visibility .28s ease;
}
.event-modal.is-open { opacity: 1; visibility: visible; pointer-events: auto; }
.event-modal-backdrop {
    position: absolute; inset: 0;
    background: rgba(4, 6, 14, .82);
    backdrop-filter: blur(8px);
    opacity: 0; transition: opacity .28s ease;
}
.event-modal.is-open .event-modal-backdrop { opacity: 1; }
.event-modal-card {
    position: relative;
    width: min(960px, calc(100% - 1rem));
    max-height: 90vh; overflow: hidden;
    border-radius: 20px;
    background: rgba(10, 14, 26, .72);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid rgba(26,115,232,.22);
    box-shadow: 0 30px 80px rgba(0,0,0,.65), 0 0 0 1px rgba(255,255,255,.04);
    z-index: 1;
    display: grid; grid-template-columns: minmax(280px,40%) 1fr;
    transform: translateY(18px) scale(.985);
    opacity: 0; transition: transform .3s ease, opacity .24s ease;
}
.event-modal-card::before {
    content: ""; position: absolute; left:0; top:0; bottom:0; width:4px;
    background: var(--maroon); border-radius: 20px 0 0 20px; z-index:2;
}
.event-modal.is-open .event-modal-card { transform: translateY(0) scale(1); opacity: 1; }
.event-modal-close {
    position: absolute; right: 1rem; top: 1rem;
    width: 2rem; height: 2rem; border-radius: 999px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.14);
    backdrop-filter: var(--glass-blur-sm);
    color: #fff; font-size: 1.1rem; line-height: 1; cursor: pointer; z-index: 2;
    transition: transform .16s, background .16s;
}
.event-modal-close:hover { transform: scale(1.08); background: rgba(160,32,46,.35); }
.event-modal-image {
    width: 100%; height: 100%;
    background-size: cover; background-position: center;
    background-color: rgba(10,14,26,.8);
}
.event-modal-content { padding: 1.4rem; overflow: auto; }
.event-modal-category { margin:0; color: rgba(255,180,185,.9); font-size:.68rem; letter-spacing:.12em; font-weight:700; text-transform:uppercase; }
.event-modal-title { margin:.3rem 0 .7rem; color:#fff; font-size:clamp(1.1rem,2.4vw,1.8rem); line-height:1.2; }
.event-modal-description { margin:0 0 1rem; color:var(--muted); font-size:.9rem; line-height:1.65; }
.event-modal-meta-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.42rem; margin-bottom:1rem; }
.event-modal-row {
    margin:0; display:flex; flex-direction:column; gap:.16rem;
    color:rgba(220,232,255,.8); font-size:.82rem; line-height:1.4;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.09);
    backdrop-filter: var(--glass-blur-sm);
    border-radius:10px; padding:.52rem .65rem;
}
.event-modal-row strong { color:#fff; font-size:.65rem; letter-spacing:.09em; text-transform:uppercase; opacity:.75; }
.event-modal-section { margin-bottom:.9rem; }
.event-modal-section h4 { margin:0 0 .4rem; color:rgba(255,180,185,.85); font-size:.7rem; letter-spacing:.1em; text-transform:uppercase; font-weight:800; }
.event-modal-artists { display:flex; flex-wrap:wrap; gap:.38rem; }
.artist-chip {
    display:inline-flex; align-items:center; border-radius:999px;
    background: rgba(160,32,46,.15);
    border: 1px solid rgba(160,32,46,.32);
    color:rgba(255,220,222,.88); font-size:.74rem; font-weight:600; padding:.28rem .6rem;
}
.event-modal-actions { margin-top:.8rem; display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.event-modal-buy {
    display:inline-flex; align-items:center; justify-content:center;
    border-radius:999px; border:0; padding:.58rem 1.25rem;
    background: var(--blue); color:#fff; font-size:.88rem; font-weight:700;
    font-family:var(--site-font); cursor:pointer;
    box-shadow: 0 4px 16px var(--blue-glow);
    transition:background .15s;
}
.event-modal-buy:hover { background: var(--blue-hover); }
.event-modal-link {
    display:inline-flex; align-items:center; border-radius:999px;
    padding:.52rem .95rem;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    color:rgba(220,232,255,.85); font-size:.84rem; font-weight:600; text-decoration:none;
    transition:border-color .15s, color .15s;
}
.event-modal-link:hover { border-color:rgba(160,32,46,.6); color:#fff; }
.event-payment-options {
    margin-top:.8rem;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.09);
    backdrop-filter: var(--glass-blur-sm);
    border-radius:12px; padding:.85rem;
}
.event-payment-title { margin:0 0 .4rem; color:rgba(220,232,255,.8); font-size:.74rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
.event-payment-amount { margin:0 0 .6rem; color:var(--muted); font-size:.82rem; }
.event-payment-amount strong { color:#fff; }
.event-payment-qty-wrap { display:flex; align-items:center; justify-content:space-between; gap:.7rem; margin-bottom:.5rem; }
.event-payment-qty-label { color:rgba(220,232,255,.8); font-size:.8rem; font-weight:700; }
.event-payment-qty {
    display:inline-flex; align-items:center; gap:.35rem;
    border: 1px solid rgba(255,255,255,.1); border-radius:999px;
    background: rgba(255,255,255,.05); padding:.2rem;
}
 qty-btn {
    width:1.7rem; height:1.7rem; border-radius:999px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    color:rgba(220,232,255,.85); font-size:.95rem; font-weight:700; line-height:1; cursor:pointer;
    transition:border-color .15s;
}
 qty-btn:hover { border-color:var(--blue); color:#fff; }
 qty-value { min-width:1.8rem; text-align:center; color:#fff; font-size:.85rem; font-weight:700; }
.event-payment-grid { display:grid; grid-template-columns:1fr; gap:.42rem; }
.payment-option {
    display:flex; align-items:center; gap:.7rem;
    border-radius:10px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.1);
    color:rgba(220,232,255,.8); font-size:.82rem; font-weight:600;
    font-family:var(--site-font); padding:.55rem .7rem; cursor:pointer;
    transition:border-color .16s, background .16s;
}
.payment-option img { width:2rem; height:2rem; object-fit:contain; flex-shrink:0; }
.payment-option:hover { border-color:rgba(160,32,46,.55); background:rgba(160,32,46,.12); }
.payment-option.is-selected { border-color:var(--maroon); background:rgba(160,32,46,.2); color:#fff; }
body.modal-open { overflow:hidden; }

/* ── Responsive ───────────────────────────────────────────────────────── */

/* tablet landscape */
@media (max-width: 1100px) {
    .hp-grid { grid-template-columns: repeat(3,1fr); }
    .hp-package-card { grid-template-columns: 1fr; }
    .hp-package-media { min-height: 240px; transform: translateY(-24px); }
    .hp-package-card.is-active .hp-package-media {
        transform: translateY(0);
        transition: transform .6s cubic-bezier(.22,.68,0,1.1), opacity .5s ease;
    }
    /* alt cards: scale+fade only (no translateY) */
    .hp-package-card--alt .hp-package-media { transform: scale(1.04); }
    .hp-package-card--alt.is-active .hp-package-media {
        transform: scale(1);
        transition: transform .75s ease, opacity .55s ease;
    }
    .hp-package-copy { text-align: center; transform: translateY(20px); }
    .hp-package-card.is-active .hp-package-copy {
        transform: translateY(0);
        transition: transform .6s cubic-bezier(.22,.68,0,1.1) .07s, opacity .5s ease .07s;
    }
    .hp-hero-panel-packages { padding: 7rem 1.5rem 1.5rem; }
}

/* tablet portrait */
@media (max-width: 860px) {
    .hp-hero-body { padding: 7rem 1.25rem 2rem; }
    .hp-hero-panel-packages { padding: 6rem 1.25rem 1.25rem; }
    .hp-packages-heading { max-height: 12rem; }
    .hp-arrow { display: none; }
    .hp-grid { grid-template-columns: repeat(2,1fr); gap: .8rem; }
    .hp-cat-bar { top: 3.9rem; }
    .hp-cat-bar-inner { padding: .58rem 1rem; }
    .event-modal-card { grid-template-columns:1fr; grid-template-rows:220px 1fr; max-height:92vh; }
    .event-modal-meta-grid { grid-template-columns:1fr; }
}

/* mobile */
@media (max-width: 640px) {
    .hp-hero { min-height: 580px; }
    .hp-hero-body { padding: 5.8rem 1rem 1.5rem; text-align: center; }
    .hp-hero-panel-packages { padding: 5.5rem 1rem 1rem; }
    .hp-packages-heading { max-height: 14rem; }
    .hp-hero-heading { font-size: 1.75rem; letter-spacing: -.02em; }
    .hp-hero-sub { font-size: .85rem; margin-bottom: 1.4rem; }

    /* package card */
    .hp-package-stage { margin-top: .4rem; }
    .hp-package-track { min-height: 300px; }
    .hp-package-card { padding: .8rem; border-radius: 20px; }
    .hp-package-media { min-height: 160px; }
    .hp-package-label { font-size: .72rem; margin-bottom: .5rem; }
    .hp-package-title { font-size: 1.15rem; margin-bottom: .5rem; }
    .hp-package-text { font-size: .82rem; line-height: 1.55; }
    .hp-package-dots { margin-top: .55rem; }

    /* search bar: stack */
    .hp-search-bar { flex-wrap: wrap; padding: .55rem .55rem .55rem 1rem; border-radius: 16px; gap: .4rem; }
    .hp-search-input { width: 100%; order: 2; }
    .hp-search-icon  { order: 1; }
    .hp-search-btn   { order: 3; width: 100%; border-radius: 10px; padding: .6rem 1rem; }


    /* category bar */
    .hp-cat-bar { top: 3.5rem; }
    .hp-cat-bar-inner { padding: .5rem .75rem; gap: .35rem; }

    /* sections */
    .hp-featured, .hp-all { padding: 2rem .75rem 2.5rem; }
    .hp-sec-head { flex-wrap: wrap; gap: .5rem; margin-bottom: .9rem; }
    .hp-sec-title { font-size: 1.15rem; }
    .hp-see-all { font-size: .8rem; }

    /* featured strip */
    .hp-fcard { flex: 0 0 80vw; }

    /* grid: single column */
    .hp-grid { grid-template-columns: 1fr; gap: .75rem; }

    /* modal: bottom sheet */
    .event-modal { padding: 0; align-items: flex-end; }
    .event-modal-card {
        width: 100%; max-height: 95vh;
        border-radius: 20px 20px 0 0;
        grid-template-columns: 1fr;
        grid-template-rows: 200px 1fr;
    }
    .event-modal-card::before { display: none; }
    .event-modal-content { padding: 1rem; }
}

/* small phones */
@media (max-width: 420px) {
    .hp-hero { min-height: 560px; }
    .hp-hero-panel-packages { padding: 5rem .75rem .75rem; }
    .hp-packages-heading { max-height: 16rem; }
    .hp-package-track { min-height: 280px; }
    .hp-package-media { min-height: 140px; }
    .hp-package-title { font-size: 1.05rem; }
    .hp-package-text { font-size: .79rem; }
    .hp-package-dot { width: 9px; height: 9px; }
    .hp-cat-bar-inner { padding: .45rem .6rem; gap: .3rem; }
    .hp-cat { font-size: .72rem; padding: .32rem .6rem; }
    .hp-fcard { flex: 0 0 88vw; }
    .hp-ecard-title { font-size: .88rem; }
}
</style>

{{-- ─────────────────────── SCRIPTS ─────────────────────── --}}
<script>
(() => {
    // hero intro + ticket packages sequence
    const heroEl          = document.getElementById('hp-hero');
    const heroIntroBg     = document.querySelector('.hp-hero-slide-intro');
    const heroPackageBg   = document.querySelector('.hp-hero-slide-package');
    const heroIntroPanel  = document.querySelector('.hp-hero-panel-intro');
    const heroPackagePanel = document.querySelector('.hp-hero-panel-packages');
    const packageCards    = Array.from(document.querySelectorAll('.hp-package-card'));
    const packageDots     = Array.from(document.querySelectorAll('.hp-package-dot'));

    if (heroEl && heroIntroBg && heroPackageBg && heroIntroPanel && heroPackagePanel && packageCards.length) {
        const introDelay = 5000;
        const packageDelay = 4200;
        let packageIndex = 0;
        let phaseTimer = null;
        let packageTimer = null;

        const setPackage = (index) => {
            packageIndex = index;
            heroEl.classList.toggle('is-first-package', index === 0);
            packageCards.forEach((card, cardIndex) => {
                card.classList.toggle('is-active', cardIndex === index);
            });
            packageDots.forEach((dot, dotIndex) => {
                const isActive = dotIndex === index;
                dot.classList.toggle('is-active', isActive);
                dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        };

        const showIntro = () => {
            heroEl.classList.remove('is-packages', 'is-first-package');
            heroIntroBg.classList.add('is-active');
            heroPackageBg.classList.remove('is-active');
            heroIntroPanel.classList.add('is-active');
            heroPackagePanel.classList.remove('is-active');
        };

        const showPackages = () => {
            heroEl.classList.add('is-packages', 'is-first-package');
            heroIntroBg.classList.remove('is-active');
            heroPackageBg.classList.add('is-active');
            heroIntroPanel.classList.remove('is-active');
            heroPackagePanel.classList.add('is-active');
        };

        const clearHeroTimers = () => {
            clearTimeout(phaseTimer);
            clearInterval(packageTimer);
        };

        const scheduleRestart = () => {
            clearHeroTimers();
            showIntro();
            phaseTimer = setTimeout(startPackageShowcase, introDelay);
        };

        const startPackageRotation = () => {
            clearInterval(packageTimer);
            packageTimer = setInterval(() => {
                const nextIndex = packageIndex + 1;
                if (nextIndex >= packageCards.length) {
                    scheduleRestart();
                    return;
                }

                setPackage(nextIndex);
            }, packageDelay);
        };

        const startPackageShowcase = () => {
            clearHeroTimers();
            showPackages();
            setPackage(0);
            startPackageRotation();
        };

        packageDots.forEach(dot => {
            dot.addEventListener('click', () => {
                const nextIndex = Number(dot.dataset.packageIndex || 0);
                clearHeroTimers();
                showPackages();
                setPackage(nextIndex);
                startPackageRotation();
            });
        });

        showIntro();
        phaseTimer = setTimeout(startPackageShowcase, introDelay);
    }

    // category + live search filter
    const catBtns   = document.querySelectorAll('.hp-cat');
    const featCards = document.querySelectorAll('.hp-fcard');
    const gridCards = document.querySelectorAll('.hp-ecard');
    const gridEmpty = document.getElementById('hp-grid-empty');
    const searchInput = document.getElementById('hp-search-input');

    let activeCategory = 'all';
    let searchQuery    = '';

    const applyFilters = () => {
        let visible = 0;
        gridCards.forEach(card => {
            const catOk  = activeCategory === 'all' || card.dataset.category === activeCategory;
            const txtOk  = !searchQuery || (card.dataset.titleSearch || '').includes(searchQuery);
            const show   = catOk && txtOk;
            card.classList.toggle('is-hidden', !show);
            if (show) visible++;
        });
        if (gridEmpty) gridEmpty.hidden = visible > 0;
        featCards.forEach(card => {
            card.style.display = (activeCategory === 'all' || card.dataset.category === activeCategory) ? '' : 'none';
        });
    };

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.category || 'all';
            // Sync active state across hero chips AND sticky bar chips
            catBtns.forEach(b => {
                const active = (b.dataset.category || 'all') === cat;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            activeCategory = cat;
            applyFilters();
            // Scroll to featured section so filtered results are visible
            const target = document.querySelector('.hp-featured');
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            searchQuery = searchInput.value.trim().toLowerCase();
            applyFilters();
        });
        const form = document.getElementById('hp-search-form');
        if (form) form.addEventListener('submit', e => { if (!searchQuery) e.preventDefault(); });
    }

    // ticket packages marquee
    const marquee = document.querySelector('.marquee');
    const marqueeTrack = document.querySelector('.marquee-content');
    if (marquee && marqueeTrack) {
        let offset = 0;
        let rafId = null;
        let paused = false;
        const gap = () => parseFloat(getComputedStyle(marqueeTrack).gap || '0');
        const speed = () => window.innerWidth <= 640 ? 0.42 : 0.56;

        const step = () => {
            if (!paused) {
                offset -= speed();
                const first = marqueeTrack.firstElementChild;
                if (first) {
                    const firstWidth = first.getBoundingClientRect().width + gap();
                    if (Math.abs(offset) >= firstWidth) {
                        offset += firstWidth;
                        marqueeTrack.appendChild(first);
                    }
                }
                marqueeTrack.style.transform = `translateX(${offset}px)`;
            }

            rafId = window.requestAnimationFrame(step);
        };

        marquee.addEventListener('mouseenter', () => { paused = true; });
        marquee.addEventListener('mouseleave', () => { paused = false; });
        marquee.addEventListener('touchstart', () => { paused = true; }, { passive: true });
        marquee.addEventListener('touchend', () => { paused = false; }, { passive: true });

        rafId = window.requestAnimationFrame(step);
        window.addEventListener('beforeunload', () => {
            if (rafId) window.cancelAnimationFrame(rafId);
        });
    }

    // featured strip arrows
    const track   = document.getElementById('hp-feat-track');
    const prevBtn = document.getElementById('hp-feat-prev');
    const nextBtn = document.getElementById('hp-feat-next');
    if (track && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => track.scrollBy({ left: -330, behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => track.scrollBy({ left:  330, behavior: 'smooth' }));
    }

    // modal
    const modal         = document.getElementById('event-modal');
    const modalImage    = document.getElementById('event-modal-image');
    const modalCategory = document.getElementById('event-modal-category');
    const modalTitle    = document.getElementById('event-modal-title');
    const modalDate     = document.getElementById('event-modal-date');
    const modalTime     = document.getElementById('event-modal-time');
    const modalLocation = document.getElementById('event-modal-location');
    const modalPrice    = document.getElementById('event-modal-price');
    const modalLink     = document.getElementById('event-modal-link');
    const modalDesc     = document.getElementById('event-modal-description');
    const modalArtists  = document.getElementById('event-modal-artists');
    const modalArtistsSec = document.getElementById('event-modal-artists-section');
    const modalBuy      = document.getElementById('event-modal-buy');
    const paymentOpts   = document.getElementById('event-payment-options');
    const paymentAmt    = document.getElementById('event-payment-amount');
    const paymentBtns   = document.querySelectorAll('.payment-option');
    const qtyMinus      = document.getElementById('ticket-qty-minus');
    const qtyPlus       = document.getElementById('ticket-qty-plus');
    const qtyVal        = document.getElementById('ticket-qty-value');
    if (!modal) return;

    let unitPrice = 0, qty = 1;
    const parsePriceToNum = t => (!t || /free/i.test(t)) ? 0 : Number((t.replace(/[^0-9.]/g,'')||'0'));
    const fmtUgx = n => (!n||isNaN(n)) ? 'Free' : `UGX ${Math.round(n).toLocaleString('en-US')}`;
    const updateTotal = () => {
        if (qtyVal) qtyVal.textContent = String(qty);
        if (paymentAmt) paymentAmt.textContent = fmtUgx(unitPrice * qty);
        if (modalBuy) modalBuy.textContent = unitPrice <= 0 ? 'Get Ticket →' : 'Buy Ticket';
    };

    const dataFromCard = card => ({
        image:       card.dataset.image || '',
        category:    card.dataset.category || '',
        title:       card.dataset.title || card.querySelector('.hp-ecard-title,.hp-fcard-title')?.textContent || '',
        description: card.dataset.description || '',
        artists:     (() => { try { return JSON.parse(card.dataset.artists||'[]'); } catch { return []; } })(),
        href:        card.dataset.href || '/events',
        location:    card.dataset.location || '',
        time:        card.dataset.time || '',
        price:       card.dataset.price || '',
        date:        card.dataset.date || '',
    });

    const openModal = data => {
        modalImage.style.backgroundImage = data.image ? `url('${data.image}')` : 'none';
        modalCategory.textContent = data.category || 'Event';
        modalTitle.textContent    = data.title    || 'Event Details';
        modalDate.textContent     = data.date     || 'Upcoming';
        modalTime.textContent     = data.time     || 'TBA';
        modalLocation.textContent = data.location || 'Venue details available';
        modalPrice.textContent    = data.price    || 'Check listing';
        modalDesc.textContent     = data.description || '';
        modalLink.href            = data.href     || '/events';
        unitPrice = parsePriceToNum(data.price); qty = 1;
        updateTotal();
        if (paymentOpts) paymentOpts.hidden = true;
        modalArtists.innerHTML = '';
        (data.artists||[]).forEach(a => { const c=document.createElement('span'); c.className='artist-chip'; c.textContent=a; modalArtists.appendChild(c); });
        if (modalArtistsSec) modalArtistsSec.hidden = !(data.artists?.length);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden','false');
        document.body.classList.add('modal-open');
    };
    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden','true');
        document.body.classList.remove('modal-open');
        if (paymentOpts) paymentOpts.hidden = true;
        paymentBtns.forEach(b => b.classList.remove('is-selected'));
        qty = 1;
    };

    document.querySelectorAll('.hp-fcard,.hp-ecard').forEach(card => {
        card.setAttribute('tabindex','0');
        card.addEventListener('click', e => { if (e.target.closest('a')) return; openModal(dataFromCard(card)); });
        card.addEventListener('keydown', e => { if (e.key!=='Enter'&&e.key!==' ') return; e.preventDefault(); openModal(dataFromCard(card)); });
    });

    modal.querySelectorAll('[data-modal-close]').forEach(el => el.addEventListener('click', closeModal));
    document.addEventListener('keydown', e => { if (e.key==='Escape'&&modal.classList.contains('is-open')) closeModal(); });

    if (modalBuy) {
        modalBuy.addEventListener('click', () => {
            window.location.href = `${modalLink.href}/checkout`;
        });
    }
})();
</script>

@endsection
