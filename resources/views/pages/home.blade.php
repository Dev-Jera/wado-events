@extends('layouts.app')

@php
    $categoryPills = collect($categoryPills ?? []);
    if ($categoryPills->isEmpty()) {
        $allForPills = $featuredEvents->merge($trendingEvents)->merge($upcomingEvents)->merge($allPublished);
        $categoryPills = $allForPills
            ->groupBy(fn ($e) => \Illuminate\Support\Str::lower($e->category_label ?? 'uncategorized'))
            ->map(fn ($events) => [
                'label' => $events->first()->category_label,
                'count' => $events->count(),
            ]);
    }
    $hasAnyEvents = $featuredEvents->isNotEmpty() || $trendingEvents->isNotEmpty() || $upcomingEvents->isNotEmpty();


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

    $eventImageUrl = static function (?string $path): string {
        $path = trim((string) $path);

        if ($path === '') {
            return asset('images/movie.jpg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'storage/') || str_starts_with($normalizedPath, 'images/')) {
            return asset($normalizedPath);
        }

        if (str_starts_with($normalizedPath, 'event-images/')) {
            return asset('storage/' . $normalizedPath);
        }

        return asset($normalizedPath);
    };

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
            <button class="hp-cat is-active" type="button" data-category="all" aria-pressed="true">
                <svg class="hp-cat-icon" aria-hidden="true"><use href="#icon-community"/></svg>
                All
            </button>
            @foreach ($categoryPills as $key => $cat)
                <button class="hp-cat" type="button" data-category="{{ $key }}" data-url="{{ route('events.index', ['category' => $key]) }}" aria-pressed="false">
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
            <button class="hp-cat" data-category="{{ $key }}" data-url="{{ route('events.index', ['category' => $key]) }}" aria-pressed="false" type="button">
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
@if($hpSettings->show_featured && $featuredEvents->isNotEmpty())
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
                @foreach ($featuredEvents as $event)
                    @php $price = (float) $event->ticket_price; @endphp
                    <article class="hp-fcard"
                        data-category="{{ \Illuminate\Support\Str::lower($event->category_label ?? 'uncategorized') }}"
                        data-ts="{{ $event->starts_at->timestamp }}"
                        data-href="{{ $event->url }}"
                        data-title="{{ $event->title }}"
                        data-description="{{ e($event->description) }}"
                        data-artists='@json(collect($event->artists ?? [])->pluck("name")->values())'
                        data-location="{{ e($event->venue.', '.$event->city) }}"
                        data-time="{{ $event->starts_at->format('h:i A') }}"
                        data-price="{{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}"
                        data-date="{{ $event->starts_at->format('d M') }}"
                        data-checkout="{{ route('checkout.create', $event) }}"
                        data-image="{{ $eventImageUrl($event->image_url) }}">

                        <div class="hp-fcard-img"
                             style="background-image:url('{{ $eventImageUrl($event->image_url) }}')">
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
                                   onclick="event.stopPropagation()">Get Tickets</a>
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
@endif

{{-- ─────────────────────── TRENDING STRIP ─────────────────────── --}}
@if($hpSettings->show_trending && $trendingEvents->isNotEmpty())
<section class="hp-featured hp-trending" aria-label="Trending events">
    <div class="hp-shell">
        <div class="hp-sec-head">
            <div>
                <p class="hp-sec-kicker">HOT RIGHT NOW</p>
                <h2 class="hp-sec-title">Trending Events</h2>
            </div>
            <a href="{{ route('events.index') }}" class="hp-see-all">
                See all <svg aria-hidden="true"><use href="#icon-arrow-r"/></svg>
            </a>
        </div>

        <div class="hp-strip-wrap">
            <div class="hp-strip" id="hp-trend-track">
                @foreach ($trendingEvents as $event)
                    @php $price = (float) $event->ticket_price; @endphp
                    <article class="hp-fcard"
                        data-category="{{ \Illuminate\Support\Str::lower($event->category_label ?? 'uncategorized') }}"
                        data-ts="{{ $event->starts_at->timestamp }}"
                        data-href="{{ $event->url }}"
                        data-title="{{ $event->title }}"
                        data-description="{{ e($event->description) }}"
                        data-artists='@json(collect($event->artists ?? [])->pluck("name")->values())'
                        data-location="{{ e($event->venue.', '.$event->city) }}"
                        data-time="{{ $event->starts_at->format('h:i A') }}"
                        data-price="{{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}"
                        data-date="{{ $event->starts_at->format('d M') }}"
                        data-checkout="{{ route('checkout.create', $event) }}"
                        data-image="{{ $eventImageUrl($event->image_url) }}">

                        <div class="hp-fcard-img"
                             style="background-image:url('{{ $eventImageUrl($event->image_url) }}')">
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
                                   onclick="event.stopPropagation()">Get Tickets</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <button class="hp-arrow hp-arrow-prev" id="hp-trend-prev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="hp-arrow hp-arrow-next" id="hp-trend-next" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        </div>
    </div>
</section>
@endif

{{-- ─────────────────────── UPCOMING GRID ─────────────────────── --}}
@if($hpSettings->show_upcoming && $upcomingEvents->isNotEmpty())
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
            @foreach ($upcomingEvents as $event)
                @php
                    $key   = \Illuminate\Support\Str::lower($event->category_label ?? 'uncategorized');
                    $price = (float) $event->ticket_price;
                @endphp
                <article class="hp-ecard"
                    data-category="{{ $key }}"
                    data-ts="{{ $event->starts_at->timestamp }}"
                    data-title-search="{{ \Illuminate\Support\Str::lower($event->title) }}"
                    data-description="{{ e($event->description) }}"
                    data-artists='@json(collect($event->artists ?? [])->pluck("name")->values())'
                    data-href="{{ $event->url }}"
                    data-location="{{ e($event->venue.', '.$event->city) }}"
                    data-time="{{ $event->starts_at->format('h:i A') }}"
                    data-price="{{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}"
                    data-date="{{ $event->starts_at->format('d M') }}"
                    data-checkout="{{ route('checkout.create', $event) }}"
                    data-image="{{ $eventImageUrl($event->image_url) }}">

                    <div class="hp-ecard-thumb"
                         style="background-image:url('{{ $eventImageUrl($event->image_url) }}')">
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

        <div class="hp-grid-footer">
            <a href="{{ route('events.index') }}" class="hp-browse-btn">Browse All Events</a>
        </div>
    </div>
</section>
@endif

{{-- ── Category filter empty state — always in DOM so JS can always find it ── --}}
<div class="hp-shell">
    <div class="hp-empty" id="hp-grid-empty" hidden>
        <div class="hp-empty-glow" aria-hidden="true"></div>

        <div class="hp-empty-art" aria-hidden="true">
            <svg viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="140" cy="90" r="82" stroke="rgba(192,40,60,.13)" stroke-width="1" stroke-dasharray="3 6"/>
                <circle cx="140" cy="90" r="60" stroke="rgba(192,40,60,.09)" stroke-width="1" stroke-dasharray="2 7"/>
                <rect x="30" y="52" width="220" height="76" rx="13"
                      fill="rgba(192,40,60,.13)" stroke="rgba(192,40,60,.45)" stroke-width="1.6"/>
                <circle cx="30" cy="90" r="11" fill="#2a1015"/>
                <circle cx="250" cy="90" r="11" fill="#2a1015"/>
                <line x1="68" y1="54" x2="68" y2="126"
                      stroke="rgba(192,40,60,.35)" stroke-width="1.3" stroke-dasharray="4 5"/>
                <path d="M49 90 L50.4 86 L54.5 86 L51.3 88.5 L52.7 92.5 L49 90.3 L45.3 92.5 L46.7 88.5 L43.5 86 L47.6 86 Z"
                      fill="rgba(255,180,185,.55)"/>
                <rect x="98" y="63" width="82" height="54" rx="9"
                      fill="rgba(255,255,255,.05)" stroke="rgba(255,255,255,.11)" stroke-width="1.2"/>
                <rect x="98" y="63" width="82" height="16" rx="9" fill="rgba(192,40,60,.22)"/>
                <rect x="98" y="70" width="82" height="9" fill="rgba(192,40,60,.22)"/>
                <line x1="119" y1="60" x2="119" y2="68" stroke="rgba(255,180,185,.75)" stroke-width="2" stroke-linecap="round"/>
                <line x1="161" y1="60" x2="161" y2="68" stroke="rgba(255,180,185,.75)" stroke-width="2" stroke-linecap="round"/>
                <text x="139" y="108" text-anchor="middle" fill="rgba(255,180,185,.4)"
                      font-size="22" font-weight="900" font-family="system-ui,sans-serif">?</text>
                <path d="M18 28 L19.5 23 L24 23 L20.5 26 L22 31 L18 28.5 L14 31 L15.5 26 L12 23 L16.5 23 Z"
                      fill="rgba(192,40,60,.5)"/>
                <path d="M232 20 L233.2 16.5 L237 16.5 L234.2 18.7 L235.4 22.2 L232 20.2 L228.6 22.2 L229.8 18.7 L227 16.5 L230.8 16.5 Z"
                      fill="rgba(255,180,185,.55)"/>
                <circle cx="256" cy="130" r="4"   fill="rgba(192,40,60,.32)"/>
                <circle cx="268" cy="118" r="2.2" fill="rgba(192,40,60,.22)"/>
                <circle cx="18"  cy="132" r="3"   fill="rgba(192,40,60,.28)"/>
                <circle cx="140" cy="14"  r="2.5" fill="rgba(255,180,185,.35)"/>
            </svg>
        </div>

        <div class="hp-empty-body">
            <h3 class="hp-empty-heading" id="hp-empty-heading">No upcoming events in this category</h3>
            <p class="hp-empty-sub" id="hp-empty-sub">
                Nothing scheduled here yet. Check back soon or browse all events.
            </p>
            <div class="hp-empty-actions">
                <button type="button" class="hp-empty-clear" id="hp-empty-clear">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                    All categories
                </button>
                <a href="{{ route('events.index') }}" class="hp-empty-browse">
                    Browse all events
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────── ALL-EVENTS FALLBACK ─────────────────────── --}}
@if(!$hasAnyEvents && $allPublished->isNotEmpty())
<section class="hp-all" aria-label="Events">
    <div class="hp-shell">
        <div class="hp-sec-head">
            <div>
                <p class="hp-sec-kicker">BROWSE EVENTS</p>
                <h2 class="hp-sec-title">{{ $hpSettings->empty_heading ?? 'Events' }}</h2>
            </div>
            <a href="{{ route('events.index') }}" class="hp-see-all">
                Browse all <svg aria-hidden="true"><use href="#icon-arrow-r"/></svg>
            </a>
        </div>
        <div class="hp-grid">
            @foreach ($allPublished as $event)
                @php
                    $key   = \Illuminate\Support\Str::lower($event->category_label ?? 'uncategorized');
                    $price = (float) $event->ticket_price;
                @endphp
                <article class="hp-ecard"
                    data-category="{{ $key }}"
                    data-ts="{{ $event->starts_at->timestamp }}"
                    data-title-search="{{ \Illuminate\Support\Str::lower($event->title) }}"
                    data-description="{{ e($event->description) }}"
                    data-artists='@json(collect($event->artists ?? [])->pluck("name")->values())'
                    data-href="{{ $event->url }}"
                    data-location="{{ e($event->venue.', '.$event->city) }}"
                    data-time="{{ $event->starts_at->format('h:i A') }}"
                    data-price="{{ $price <= 0 ? 'Free' : 'UGX '.number_format($price,0) }}"
                    data-date="{{ $event->starts_at->format('d M') }}"
                    data-checkout="{{ route('checkout.create', $event) }}"
                    data-image="{{ $eventImageUrl($event->image_url) }}">

                    <div class="hp-ecard-thumb"
                         style="background-image:url('{{ $eventImageUrl($event->image_url) }}')">
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
        <div class="hp-grid-footer">
            <a href="{{ route('events.index') }}" class="hp-browse-btn">Browse All Events</a>
        </div>
    </div>
</section>
@endif

{{-- ─────────────────────── FULLY EMPTY STATE ─────────────────────── --}}
@if(!$hasAnyEvents && $allPublished->isEmpty())
<section class="hp-all" aria-label="No events">
    <div class="hp-shell">
        <div class="hp-empty" style="display:block">
            <div class="hp-empty-glow" aria-hidden="true"></div>
            <div class="hp-empty-art" aria-hidden="true">
                <svg viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="140" cy="90" r="82" stroke="rgba(192,40,60,.13)" stroke-width="1" stroke-dasharray="3 6"/>
                    <circle cx="140" cy="90" r="60" stroke="rgba(192,40,60,.09)" stroke-width="1" stroke-dasharray="2 7"/>
                    <rect x="30" y="52" width="220" height="76" rx="13" fill="rgba(192,40,60,.13)" stroke="rgba(192,40,60,.45)" stroke-width="1.6"/>
                    <circle cx="30" cy="90" r="11" fill="#2a1015"/>
                    <circle cx="250" cy="90" r="11" fill="#2a1015"/>
                    <line x1="68" y1="54" x2="68" y2="126" stroke="rgba(192,40,60,.35)" stroke-width="1.3" stroke-dasharray="4 5"/>
                    <text x="139" y="108" text-anchor="middle" fill="rgba(255,180,185,.4)" font-size="22" font-weight="900" font-family="system-ui,sans-serif">?</text>
                </svg>
            </div>
            <div class="hp-empty-body">
                <h3 class="hp-empty-heading">{{ $hpSettings->empty_heading ?? 'No upcoming events right now' }}</h3>
                <p class="hp-empty-sub">{{ $hpSettings->empty_sub ?? "We're working on bringing more events to you. Check back soon." }}</p>
                <div class="hp-empty-actions">
                    <a href="{{ route('events.index') }}" class="hp-empty-browse">
                        Browse all events
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

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
                <button class="event-modal-buy" id="event-modal-buy" type="button">Get Tickets</button>
                <button class="event-modal-link" id="event-modal-poster" type="button">View Poster</button>
            </div>
        </div>
    </article>
</div>

<div class="poster-modal" id="poster-modal" aria-hidden="true">
    <div class="poster-modal-backdrop" data-poster-close></div>
    <article class="poster-modal-card" role="dialog" aria-modal="true" aria-label="Full event poster">
        <button class="poster-modal-close" type="button" data-poster-close aria-label="Close poster">×</button>
        <img class="poster-modal-image" id="poster-modal-image" alt="Event poster preview" />
    </article>
</div>

{{-- ─────────────────────── STYLES ─────────────────────── --}}
<style>
/* ── Root ───────────────────────────────────────────────────────────── */
:root {
    --bg:           #1b1115;
    --surface:      #26161c;
    --surface-soft: #2e1a21;
    --border-soft:  rgba(255,255,255,.1);
    --text-main:    #f7f4f5;
    --text-sub:     rgba(247,244,245,.78);
    --maroon:       #c0283c;
    --maroon-hover: #a01e2e;
    --maroon-glow:  rgba(192, 40, 60, .26);
    --maroon-glass: rgba(192, 40, 60, .12);
    --blue:         #1255c0;
    --blue-hover:   #0e3fa0;
    --blue-glow:    rgba(18, 85, 192, .24);
    --green:        #22c55e;
    --green-dk:     #16a34a;

    /* kept for shared controls, but toned down */
    --glass-bg:      rgba(255, 255, 255, .04);
    --glass-bg-md:   rgba(255, 255, 255, .06);
    --glass-bg-hi:   rgba(255, 255, 255, .1);
    --glass-border:  rgba(255, 255, 255, .12);
    --glass-blur:    blur(20px);
    --glass-blur-sm: blur(12px);

    /* text */
    --txt:   var(--text-main);
    --muted: var(--text-sub);
}

html, body { margin: 0; padding: 0; }
.icon-sprite { position: absolute; width: 0; height: 0; overflow: hidden; }

/* ── Page background — lighter warm dark, minimal gradient ── */
body {
    background:
        radial-gradient(ellipse 64% 46% at 0% 0%, rgba(192, 40, 60, .12) 0%, transparent 56%),
        radial-gradient(ellipse 46% 36% at 100% 0%, rgba(18, 85, 192, .08) 0%, transparent 62%),
        radial-gradient(ellipse 42% 32% at 100% 100%, rgba(192, 40, 60, .05) 0%, transparent 66%),
        var(--bg);
    background-attachment: fixed;
}

/* ── HERO ────────────────────────────────────────────────────────────── */
.hp-hero {
    position: relative;
    height: 62vh;
    margin-top: -8px;
    min-height: 610px;
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
    background: linear-gradient(160deg, #1f1317 0%, #2a171d 55%, #171013 100%);
}

/* dark veil over slide 1 photo — fades away on slide 2 */
.hp-hero-veil {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: linear-gradient(
        160deg,
        rgba(12, 9, 11, .62) 0%,
        rgba(12, 9, 11, .46) 48%,
        rgba(12, 9, 11, .78) 100%
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
    padding: 8.8rem 1.5rem 3rem;
    text-align: center;
    margin-inline: auto;
}
.hp-hero-panel-intro  { max-width: 680px; }
.hp-hero-panel-intro.is-active { z-index: 4; }
.hp-hero-panel-packages {
    max-width: 1120px;
    z-index: 3;
    justify-content: flex-start;
    overflow: hidden;
    padding: 8rem 1.5rem 1.5rem;
}
.hp-hero-panel-packages .hp-hero-eyebrow {
    margin-bottom: .5rem;
    font-size: .64rem;
    letter-spacing: .11em;
    color: rgba(247, 244, 245, .68);
}
.hp-hero-panel-packages .hp-hero-heading {
    font-size: clamp(1.45rem, 2.7vw, 1.95rem);
    line-height: 1.16;
    max-width: 24ch;
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
    letter-spacing: .14em;
    color: rgba(247, 244, 245, .76);
    text-transform: uppercase;
}
.hp-hero-heading {
    margin: 0 0 .9rem;
    font-size: clamp(1.7rem, 6vw, 3.4rem);
    line-height: 1.1;
    letter-spacing: -.025em;
    font-weight: 700;
    color: var(--text-main);
    max-width: 15ch;
    margin-left: auto;
    margin-right: auto;
}
.hp-hero-sub {
    margin: 0 auto 2.2rem;
    font-size: clamp(.88rem, 1.9vw, 1.05rem);
    color: var(--text-sub);
    line-height: 1.65;
    max-width: 56ch;
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
    position: relative;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .4rem .4rem .4rem 1.1rem;
    border-radius: 999px;
    background: rgba(28, 22, 25, .88);
    border: 1px solid var(--border-soft);
    box-shadow: 0 8px 22px rgba(0,0,0,.22);
    transition: border-color .2s, box-shadow .2s;
    pointer-events: auto;
    width: min(620px, 100%);
}
.hp-search-bar:focus-within {
    border-color: rgba(255,255,255,.26);
    box-shadow: 0 8px 22px rgba(0,0,0,.24);
}
.hp-search-icon {
    width: 1.1rem; height: 1.1rem;
    stroke: rgba(255,255,255,.45); fill: none;
    stroke-width: 2; stroke-linecap: round;
    flex-shrink: 0;
}
.hp-search-input {
    position: relative;
    z-index: 2;
    flex: 1; min-width: 0;
    background: transparent; border: none; outline: none;
    color: #fff; font-size: .95rem; font-family: var(--site-font);
    pointer-events: auto;
}
.hp-search-input::placeholder { color: rgba(255,255,255,.35); }
.hp-search-btn {
    position: relative;
    z-index: 2;
    flex-shrink: 0;
    padding: .6rem 1.5rem;
    border-radius: 999px; border: none;
    background: var(--blue);
    color: #fff; font-size: .88rem; font-weight: 700;
    font-family: var(--site-font); cursor: pointer;
    box-shadow: none;
    transition: background .18s;
}
.hp-search-btn:hover { background: var(--blue-hover); }

/* ── category chips (hero) — hidden; sticky bar is used instead ── */
.hp-cats { display: none; }
.hp-cats::-webkit-scrollbar { display: none; }
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
    white-space: nowrap; flex-shrink: 0;
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
    font-size: .7rem;
    font-weight: 700;
    opacity: .55;
    background: rgba(255,255,255,.1);
    border-radius: 999px;
    padding: .05rem .38rem;
    margin-left: .1rem;
}

/* ── Category bar — always visible, sticky below navbar ── */
.hp-cat-bar {
    display: block;
    position: sticky;
    top: 4.2rem;
    z-index: 30;
    background: rgba(28, 19, 22, .96);
    border-bottom: 1px solid var(--border-soft);
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
    border-radius: 22px;
    background: rgba(27, 21, 24, .92);
    border: 1px solid rgba(255,255,255,.1);
    box-shadow: 0 10px 28px rgba(0,0,0,.22);
    opacity: 0;
    pointer-events: none;
    transition: opacity .35s ease;
}
.hp-package-card.is-active {
    opacity: 1;
    pointer-events: auto;
}
/* image fades in */
.hp-package-media {
    min-height: 280px;
    border-radius: 22px;
    overflow: hidden;
    background: rgba(255,255,255,.06);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
    opacity: 0;
}
.hp-package-card.is-active .hp-package-media {
    opacity: 1;
    transition: opacity .38s ease;
}
.hp-package-media img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
/* description fades in */
.hp-package-copy {
    text-align: left;
    padding: .45rem .3rem;
    opacity: 0;
}
.hp-package-card.is-active .hp-package-copy {
    opacity: 1;
    transition: opacity .38s ease .04s;
}

/* alt cards use the same fade behavior */
.hp-package-card--alt .hp-package-media {
    opacity: 0;
}
.hp-package-card--alt.is-active .hp-package-media {
    opacity: 1;
    transition: opacity .38s ease;
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
    background: var(--surface-soft);
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
    border-radius: 16px;
    background: var(--surface);
    border: 1px solid var(--border-soft);
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
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
    padding: 3.1rem 1rem 3.6rem;
    position: relative;
}
/* sections — solid warm dark so they read clearly */
.hp-featured {
    background: var(--surface-soft);
    border-top:    1px solid var(--border-soft);
    border-bottom: 1px solid var(--border-soft);
}
.hp-all {
    background: var(--bg);
}

.hp-shell { width: min(1220px, 100%); margin: 0 auto; }

.hp-sec-head {
    display: flex; align-items: flex-end;
    justify-content: space-between;
    gap: .9rem; margin-bottom: 1.25rem;
}
.hp-sec-kicker {
    margin: 0;
    font-size: .66rem; font-weight: 700;
    letter-spacing: .1em; color: rgba(247,244,245,.66);
}
.hp-sec-title {
    margin: .3rem 0 0;
    font-size: clamp(1.3rem, 2.4vw, 1.9rem);
    color: var(--text-main); font-weight: 700; line-height: 1.18;
}
.hp-see-all {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .84rem; font-weight: 700;
    color: rgba(247,244,245,.86); text-decoration: none; white-space: nowrap;
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
    background: rgba(22, 18, 20, .92);
    border: 1px solid rgba(255,255,255,.08);
    box-shadow: 0 8px 24px rgba(0,0,0,.22);
    transition: transform .22s ease, border-color .2s, box-shadow .22s;
}
.hp-fcard:hover {
    transform: translateY(-2px);
    border-color: rgba(255,255,255,.14);
    box-shadow: 0 10px 28px rgba(0,0,0,.26);
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
    background: rgba(16, 13, 15, .92);
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 10px; padding: .28rem .55rem;
    display: flex; flex-direction: column; align-items: center; gap: 1px; line-height: 1;
    box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
.hp-fcard-day { font-size: 1rem; font-weight: 800; color: #fff; }
.hp-fcard-mon { font-size: .56rem; font-weight: 700; letter-spacing: .07em; color: rgba(255,255,255,.72); text-transform: uppercase; }
.hp-fcard-cat-badge {
    position: absolute; bottom: .7rem; left: .75rem; z-index: 1;
    font-size: .63rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: rgba(255,255,255,.88);
    background: rgba(16, 13, 15, .85);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 6px; padding: .22rem .52rem;
}
.hp-fcard-body {
    padding: 1rem 1.05rem 1.05rem;
    display: flex; flex-direction: column; gap: .3rem;
}
.hp-fcard-title {
    margin: 0 0 .12rem;
    font-size: .98rem; font-weight: 700; color: #fff; line-height: 1.28;
    display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden;
}
.hp-fcard-meta {
    display: flex; align-items: center; gap: .3rem; margin: 0;
    font-size: .78rem; color: rgba(255,255,255,.72); line-height: 1.4;
}
.hp-fcard-meta svg { width: .78rem; height: .78rem; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; opacity: .65; }
.hp-fcard-foot {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: .56rem; padding-top: .82rem;
    border-top: 1px solid rgba(255,255,255,.09);
}
.hp-fcard-price { font-size: .88rem; font-weight: 700; color: #fff; }
.hp-fcard-price.is-free { color: var(--green); }
.hp-fcard-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: .38rem .9rem; border-radius: 999px;
    background: var(--blue); color: #fff;
    font-size: .78rem; font-weight: 600; text-decoration: none;
    box-shadow: none;
    transition: background .18s, transform .15s;
}
.hp-fcard-btn:hover { background: var(--blue-hover); transform: none; }

/* ── Past event state ── */
.hp-fcard.is-past .hp-fcard-img,
.hp-ecard.is-past .hp-ecard-thumb {
    filter: grayscale(35%) brightness(.78);
}
.hp-fcard.is-past .hp-fcard-btn,
.hp-ecard.is-past .hp-ecard-btn {
    opacity: .45;
    pointer-events: none;
}
.hp-past-badge {
    position: absolute;
    top: .6rem;
    right: .6rem;
    padding: .22rem .6rem;
    border-radius: 6px;
    background: rgba(10,10,14,.86);
    border: 1px solid rgba(255,255,255,.18);
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(255,255,255,.72);
}

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
    background: rgba(22, 18, 20, .92);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px; overflow: hidden; cursor: pointer;
    display: flex; flex-direction: column;
    box-shadow: 0 8px 24px rgba(0,0,0,.22);
    transition: transform .22s ease, border-color .2s, box-shadow .22s;
}
.hp-ecard:hover {
    transform: translateY(-2px);
    border-color: rgba(255,255,255,.14);
    box-shadow: 0 10px 28px rgba(0,0,0,.26);
}
.hp-ecard.is-hidden { display: none; }

.hp-ecard-thumb {
    width: 100%; aspect-ratio: 3/2;
    background-size: cover; background-position: center;
    position: relative; flex-shrink: 0;
}
.hp-ecard-date-badge {
    position: absolute; top: .65rem; right: .65rem;
    background: rgba(16, 13, 15, .92);
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 8px; padding: .26rem .52rem;
    display: flex; flex-direction: column; align-items: center; gap: 1px; line-height: 1;
    box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
.hp-ecard-day { font-size: .95rem; font-weight: 800; color: #fff; }
.hp-ecard-mon { font-size: .53rem; font-weight: 700; letter-spacing: .07em; color: rgba(255,255,255,.72); text-transform: uppercase; }

.hp-ecard-body {
    padding: .95rem 1.05rem 1.05rem;
    display: flex; flex-direction: column; flex: 1; gap: .24rem;
}
.hp-ecard-cat {
    font-size: .66rem; font-weight: 700; letter-spacing: .11em;
    color: rgba(255,255,255,.6); text-transform: uppercase;
}
.hp-ecard-title {
    margin: 0 0 .28rem;
    font-size: .98rem; font-weight: 700; color: #fff; line-height: 1.28;
    display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden;
}
.hp-ecard-meta {
    display: flex; align-items: center; gap: .3rem; margin: 0;
    font-size: .78rem; color: rgba(255,255,255,.72); line-height: 1.4;
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
    font-size: .78rem; font-weight: 600; text-decoration: none;
    box-shadow: none;
    transition: background .18s;
}
.hp-ecard-btn:hover { background: var(--blue-hover); }

/* ── Empty state ─────────────────────────────────────────────────────── */
.hp-empty {
    grid-column: 1 / -1;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0;
    padding: 3rem 1.5rem 3.5rem;
    text-align: center;
    overflow: hidden;
    border-radius: 18px;
    border: 1px solid var(--border-soft);
    background: var(--surface);
}
.hp-empty-glow {
    position: absolute;
    inset: 0;
    background: none;
    pointer-events: none;
}
.hp-empty-art {
    position: relative;
    z-index: 1;
    margin-bottom: .4rem;
}
.hp-empty-art svg { width: 220px; height: auto; display: block; }
.hp-empty-body {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .6rem;
}
.hp-empty-heading {
    margin: 0;
    font-size: clamp(1.1rem, 2.5vw, 1.45rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
}
.hp-empty-sub {
    margin: 0;
    font-size: .86rem;
    color: var(--muted);
    line-height: 1.65;
    max-width: 340px;
}
.hp-empty-actions {
    display: flex;
    gap: .6rem;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: .5rem;
}
.hp-empty-clear {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .52rem 1.15rem;
    border-radius: 999px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--border-soft);
    color: var(--text-sub);
    font-size: .8rem; font-weight: 700;
    font-family: var(--site-font); cursor: pointer;
    transition: background .15s, border-color .15s;
}
.hp-empty-clear:hover { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.22); }
.hp-empty-browse {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .52rem 1.2rem;
    border-radius: 999px;
    background: var(--maroon);
    color: #fff;
    font-size: .8rem; font-weight: 700; text-decoration: none;
    box-shadow: none;
    transition: background .15s;
}
.hp-empty-browse:hover { background: var(--maroon-hover); }

/* browse button */
.hp-grid-footer { text-align: center; margin-top: 2.4rem; }
.hp-browse-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: .72rem 2.4rem; border-radius: 999px;
    background: var(--surface);
    border: 1px solid var(--border-soft);
    color: var(--text-main); font-size: .9rem; font-weight: 700; text-decoration: none;
    box-shadow: none;
    transition: background .2s, border-color .2s;
}
.hp-browse-btn:hover {
    background: rgba(255,255,255,.05);
    border-color: rgba(255,255,255,.22);
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
    background: rgba(6, 5, 7, .8);
    opacity: 0; transition: opacity .28s ease;
}
.event-modal-backdrop::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 48% 36% at 30% 35%, rgba(192,40,60,.16) 0%, transparent 70%),
        radial-gradient(ellipse 42% 30% at 70% 70%, rgba(18,85,192,.12) 0%, transparent 68%);
}
.event-modal.is-open .event-modal-backdrop { opacity: 1; }
.event-modal-card {
    position: relative;
    width: min(1180px, calc(100% - 1rem));
    height: min(78vh, 760px);
    max-height: 94vh;
    overflow: hidden;
    border-radius: 16px;
    background: #1b1518;
    border: 1px solid var(--border-soft);
    box-shadow: 0 24px 56px rgba(0,0,0,.52);
    z-index: 1;
    display: grid; grid-template-columns: minmax(380px,47%) 1fr;
    transform: translateY(18px) scale(.985);
    opacity: 0; transition: transform .3s ease, opacity .24s ease;
}
.event-modal-card::before {
    content: none;
}
.event-modal.is-open .event-modal-card { transform: translateY(0) scale(1); opacity: 1; }
.event-modal-close {
    position: absolute; right: 1rem; top: 1rem;
    width: 2rem; height: 2rem; border-radius: 999px;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border-soft);
    color: #fff; font-size: 1.1rem; line-height: 1; cursor: pointer; z-index: 2;
    transition: transform .16s, background .16s;
}
.event-modal-close:hover { transform: scale(1.08); background: rgba(255,255,255,.12); }
.event-modal-image {
    width: 100%; height: 100%;
    min-height: 100%;
    background-size: cover; background-position: center;
    background-color: rgba(10,14,26,.8);
    cursor: zoom-in;
    transform: scale(1);
    transition: transform .42s ease;
    position: relative;
}
.event-modal-image::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(10, 7, 9, .48) 0%, rgba(10, 7, 9, .04) 60%);
    pointer-events: none;
}
.event-modal.is-open .event-modal-image {
    transform: scale(1.02);
}
.event-modal-content {
    padding: 1.4rem;
    overflow: auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.event-modal-content > * {
    opacity: 0;
    transform: translateY(10px);
    transition: opacity .28s ease, transform .28s ease;
}
.event-modal.is-open .event-modal-content > * {
    opacity: 1;
    transform: translateY(0);
}
.event-modal.is-open .event-modal-content > :nth-child(1) { transition-delay: .06s; }
.event-modal.is-open .event-modal-content > :nth-child(2) { transition-delay: .1s; }
.event-modal.is-open .event-modal-content > :nth-child(3) { transition-delay: .14s; }
.event-modal.is-open .event-modal-content > :nth-child(4) { transition-delay: .18s; }
.event-modal.is-open .event-modal-content > :nth-child(5) { transition-delay: .22s; }
.event-modal.is-open .event-modal-content > :nth-child(6) { transition-delay: .26s; }

.event-modal-category { margin:0; color: rgba(255,205,210,.95); font-size:.66rem; letter-spacing:.16em; font-weight:800; text-transform:uppercase; }
.event-modal-title { margin:.32rem 0 .64rem; color:#fff; font-size:clamp(1.22rem,2.6vw,1.96rem); line-height:1.16; letter-spacing:-.015em; }
.event-modal-description { margin:0 0 1rem; color:var(--muted); font-size:.9rem; line-height:1.65; }
.event-modal-meta-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.42rem; margin-bottom:1rem; }
.event-modal-row {
    margin:0; display:flex; flex-direction:column; gap:.16rem;
    color: rgba(247,244,245,.78); font-size:.82rem; line-height:1.4;
    background: rgba(255,255,255,.035);
    border: 1px solid rgba(255,255,255,.1);
    border-radius:10px; padding:.52rem .65rem;
}
.event-modal-row:first-child,
.event-modal-row:last-child {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.16);
}
.event-modal-row strong { color:#fff; font-size:.65rem; letter-spacing:.09em; text-transform:uppercase; opacity:.75; }
.event-modal-section { margin-bottom:.9rem; }
.event-modal-section h4 { margin:0 0 .4rem; color:rgba(255,180,185,.85); font-size:.7rem; letter-spacing:.1em; text-transform:uppercase; font-weight:800; }
.event-modal-artists { display:flex; flex-wrap:wrap; gap:.38rem; }
.artist-chip {
    display:inline-flex; align-items:center; border-radius:999px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.12);
    color: rgba(247,244,245,.86); font-size:.74rem; font-weight:600; padding:.28rem .6rem;
}
.event-modal-actions { margin-top:.8rem; display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.event-modal-buy {
    display:inline-flex; align-items:center; justify-content:center;
    border-radius:999px; border:0; padding:.58rem 1.25rem;
    background: var(--blue); color:#fff; font-size:.88rem; font-weight:700;
    font-family:var(--site-font); cursor:pointer;
    box-shadow: 0 8px 20px rgba(18,85,192,.28);
    transition:background .15s, transform .15s, box-shadow .15s;
}
.event-modal-buy:hover { background: var(--blue-hover); transform: translateY(-1px); box-shadow: 0 10px 24px rgba(18,85,192,.34); }
.event-modal-link {
    display:inline-flex; align-items:center; border-radius:999px;
    padding:.52rem .95rem;
    background: rgba(255,255,255,.065);
    border: 1px solid rgba(255,255,255,.2);
    color: rgba(247,244,245,.92); font-size:.84rem; font-weight:600; text-decoration:none;
    transition:border-color .15s, color .15s, background .15s;
}
.event-modal-link:hover { border-color: rgba(255,255,255,.34); background: rgba(255,255,255,.1); color:#fff; }

.poster-modal {
    position: fixed; inset: 0; z-index: 140;
    display: grid; align-items: center; justify-items: center;
    padding: 1rem;
    opacity: 0; visibility: hidden; pointer-events: none;
    transition: opacity .24s ease, visibility .24s ease;
}
.poster-modal.is-open { opacity: 1; visibility: visible; pointer-events: auto; }
.poster-modal-backdrop {
    position: absolute; inset: 0;
    background: rgba(6, 5, 7, .88);
}
.poster-modal-card {
    position: relative;
    width: min(96vw, 1120px);
    max-height: 94vh;
    border-radius: 14px;
    background: #171216;
    border: 1px solid var(--border-soft);
    box-shadow: 0 18px 44px rgba(0,0,0,.62);
    overflow: hidden;
    z-index: 1;
}
.poster-modal-image {
    width: 100%;
    height: min(94vh, 920px);
    object-fit: contain;
    object-position: center;
    display: block;
    background: rgba(0,0,0,.3);
}
.poster-modal-close {
    position: absolute; right: .85rem; top: .85rem;
    width: 2rem; height: 2rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.16);
    background: rgba(0,0,0,.45);
    color: #fff;
    font-size: 1.05rem;
    cursor: pointer;
    z-index: 2;
}
body.modal-open { overflow:hidden; }

/* ── Responsive ───────────────────────────────────────────────────────── */

/* tablet landscape */
@media (max-width: 1100px) {
    .hp-grid { grid-template-columns: repeat(3,1fr); }
    .hp-package-card { grid-template-columns: 1fr; }
    .hp-package-media { min-height: 240px; }
    .hp-package-card.is-active .hp-package-media {
        transition: opacity .38s ease;
    }
    /* alt cards use same fade behavior */
    .hp-package-card--alt .hp-package-media { opacity: 0; }
    .hp-package-card--alt.is-active .hp-package-media {
        transition: opacity .38s ease;
    }
    .hp-package-copy { text-align: center; }
    .hp-package-card.is-active .hp-package-copy {
        transition: opacity .38s ease .04s;
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
    .event-modal-image {
        min-height: 220px;
        background-position: center top;
    }
    .event-modal-content { justify-content: flex-start; }
    .event-modal-meta-grid { grid-template-columns:1fr; }
    .poster-modal-card { width: min(96vw, 760px); }
}

/* mobile */
@media (max-width: 640px) {
    .hp-hero { min-height: 460px; }
    .hp-hero-body { padding: 6.2rem 1rem 1.9rem; text-align: center; }
    .hp-hero-panel-packages { padding: 5.9rem 1rem 1rem; }
    .hp-packages-heading { max-height: 14rem; }
    .hp-hero-heading { font-size: 1.68rem; letter-spacing: -.015em; line-height: 1.14; }
    .hp-hero-sub { font-size: .88rem; line-height: 1.58; margin-bottom: 1.35rem; }

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
    .hp-search-bar { flex-wrap: wrap; padding: .52rem .52rem .52rem .9rem; border-radius: 14px; gap: .38rem; }
    .hp-search-input { width: 100%; order: 2; }
    .hp-search-icon  { order: 1; }
    .hp-search-btn   { order: 3; width: 100%; border-radius: 10px; padding: .62rem .95rem; }


    .hp-cats { display: none; }
    .hp-cat-bar { top: 3.5rem; }
    .hp-cat-bar-inner { padding: .5rem .68rem; gap: .32rem; }
    .hp-cat-bar .hp-cat { flex-shrink: 0; font-size: .74rem; padding: .34rem .64rem; }

    /* sections */
    .hp-featured, .hp-all { padding: 1.85rem .72rem 2.3rem; }
    .hp-sec-head { flex-wrap: wrap; gap: .45rem; margin-bottom: .82rem; }
    .hp-sec-title { font-size: 1.12rem; }
    .hp-see-all { font-size: .8rem; }

    /* featured strip */
    .hp-fcard { flex: 0 0 80vw; }
    .hp-fcard-body { padding: .9rem .92rem .95rem; }
    .hp-fcard-title { font-size: .94rem; }
    .hp-fcard-meta { font-size: .75rem; }
    .hp-fcard-foot { padding-top: .65rem; margin-top: .48rem; }
    .hp-fcard-btn { padding: .36rem .78rem; font-size: .74rem; }

    /* grid: single column */
    .hp-grid { grid-template-columns: 1fr; gap: .75rem; }
    .hp-ecard-body { padding: .9rem .92rem .95rem; }
    .hp-ecard-title { font-size: .94rem; }
    .hp-ecard-meta { font-size: .75rem; }
    .hp-ecard-foot { padding-top: .65rem; }
    .hp-ecard-btn { padding: .36rem .78rem; font-size: .74rem; }

    /* modal: bottom sheet */
    .event-modal { padding: 0; align-items: flex-end; }
    .event-modal-card {
        width: 100%; max-height: 95vh;
        border-radius: 20px 20px 0 0;
        grid-template-columns: 1fr;
        grid-template-rows: 200px 1fr;
    }
    .event-modal-image {
        min-height: 200px;
        background-position: center top;
    }
    .event-modal-card::before { display: none; }
    .event-modal-content { padding: 1rem; }
    .poster-modal { padding: .5rem; }
    .poster-modal-card { width: 100%; max-height: 96vh; border-radius: 14px; }
    .poster-modal-image { height: min(92vh, 760px); }
}

/* small phones */
@media (max-width: 420px) {
    .hp-hero { min-height: 435px; }
    .hp-hero-body { padding: 5.6rem .82rem 1.5rem; }
    .hp-hero-panel-packages { padding: 5rem .75rem .75rem; }
    .hp-packages-heading { max-height: 16rem; }
    .hp-package-track { min-height: 280px; }
    .hp-package-media { min-height: 140px; }
    .hp-package-title { font-size: 1.02rem; }
    .hp-package-text { font-size: .79rem; }
    .hp-package-dot { width: 9px; height: 9px; }
    .hp-cat-bar-inner { padding: .45rem .6rem; gap: .3rem; }
    .hp-cat { font-size: .72rem; padding: .32rem .6rem; }
    .hp-fcard { flex: 0 0 88vw; }
    .hp-fcard-title,
    .hp-ecard-title { font-size: .9rem; }
    .hp-fcard-body,
    .hp-ecard-body { padding: .82rem .84rem .88rem; }
}

@media (prefers-reduced-motion: reduce) {
    .hp-hero-slide,
    .hp-hero-panel,
    .hp-package-media,
    .hp-package-copy,
    .hp-fcard,
    .hp-ecard,
    .event-modal-image,
    .event-modal-content > *,
    .event-modal,
    .event-modal-card,
    .poster-modal {
        transition: none !important;
        animation: none !important;
    }
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

    const catLabels = {
        'all':        'events',
        'music':      'music events',
        'sports':     'sports events',
        'theater':    'theater shows',
        'conference': 'conferences',
        'workshop':   'workshops',
        'community':  'community events',
        'comedy':     'comedy shows',
        'film':       'film screenings',
        'cinema':     'cinema screenings',
        'social':     'social events',
        'free event': 'free events',
        'free':       'free events',
    };

    const applyFilters = () => {
        let visible = 0;

        gridCards.forEach(card => {
            const catOk = activeCategory === 'all' || card.dataset.category === activeCategory;
            const txtOk = !searchQuery || (card.dataset.titleSearch || '').includes(searchQuery);
            const show  = catOk && txtOk;
            card.classList.toggle('is-hidden', !show);
            if (show) visible++;
        });

        featCards.forEach(card => {
            const show = activeCategory === 'all' || card.dataset.category === activeCategory;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (gridEmpty) {
            gridEmpty.hidden = visible > 0;
            if (visible === 0) {
                const heading = document.getElementById('hp-empty-heading');
                const sub     = document.getElementById('hp-empty-sub');
                if (heading) heading.textContent = searchQuery
                    ? `No results for "${searchQuery}"`
                    : `No upcoming events in this category`;
                if (sub) sub.innerHTML = searchQuery
                    ? `Try a different search term or clear the filter.`
                    : `Nothing scheduled here yet. Check back soon or browse all events.`;
            }
        }
    };

    const setCategory = (cat) => {
        activeCategory = cat;
        catBtns.forEach(b => {
            const active = (b.dataset.category || 'all') === cat;
            b.classList.toggle('is-active', active);
            b.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        applyFilters();
    };

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.dataset.url) {
                window.location.href = btn.dataset.url;
                return;
            }

            setCategory(btn.dataset.category || 'all');
        });
    });

    const clearBtn = document.getElementById('hp-empty-clear');
    if (clearBtn) clearBtn.addEventListener('click', () => setCategory('all'));

    if (searchInput) {
        document.getElementById('hp-search-form')?.addEventListener('click', e => {
            if (!e.target.closest('button')) {
                searchInput.focus();
            }
        });

        searchInput.addEventListener('input', () => {
            searchQuery = searchInput.value.trim().toLowerCase();
            applyFilters();
        });
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

    // trending strip arrows
    const trendTrack   = document.getElementById('hp-trend-track');
    const trendPrevBtn = document.getElementById('hp-trend-prev');
    const trendNextBtn = document.getElementById('hp-trend-next');
    if (trendTrack && trendPrevBtn && trendNextBtn) {
        trendPrevBtn.addEventListener('click', () => trendTrack.scrollBy({ left: -330, behavior: 'smooth' }));
        trendNextBtn.addEventListener('click', () => trendTrack.scrollBy({ left:  330, behavior: 'smooth' }));
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
    const modalDesc     = document.getElementById('event-modal-description');
    const modalArtists  = document.getElementById('event-modal-artists');
    const modalArtistsSec = document.getElementById('event-modal-artists-section');
    const modalBuy      = document.getElementById('event-modal-buy');
    const modalPosterBtn = document.getElementById('event-modal-poster');
    const posterModal   = document.getElementById('poster-modal');
    const posterImageEl = document.getElementById('poster-modal-image');
    const modalCloseBtn = modal.querySelector('.event-modal-close');
    const posterCloseBtn = posterModal?.querySelector('.poster-modal-close') || null;
    if (!modal) return;

    let activePosterImage = '';
    let lastFocusedElement = null;

    const getFocusableElements = container => Array.from(container.querySelectorAll(
        'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(el => !el.hasAttribute('hidden') && el.offsetParent !== null);

    const trapFocusIn = (event, container) => {
        if (event.key !== 'Tab') return;
        const focusable = getFocusableElements(container);
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    };

    const openPosterModal = imageUrl => {
        if (!posterModal || !posterImageEl || !imageUrl) return;

        lastFocusedElement = document.activeElement;
        posterImageEl.src = imageUrl;
        posterModal.classList.add('is-open');
        posterModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        posterCloseBtn?.focus();
    };

    const closePosterModal = () => {
        if (!posterModal || !posterImageEl) return;

        posterModal.classList.remove('is-open');
        posterModal.setAttribute('aria-hidden', 'true');
        posterImageEl.removeAttribute('src');
        if (lastFocusedElement instanceof HTMLElement) {
            lastFocusedElement.focus();
        }

        if (!modal.classList.contains('is-open')) {
            document.body.classList.remove('modal-open');
        }
    };

    const dataFromCard = card => ({
        image:       card.dataset.image || '',
        category:    card.dataset.category || '',
        title:       card.dataset.title || card.querySelector('.hp-ecard-title,.hp-fcard-title')?.textContent || '',
        description: card.dataset.description || '',
        artists:     (() => { try { return JSON.parse(card.dataset.artists||'[]'); } catch { return []; } })(),
        checkout:    card.dataset.checkout || '',
        location:    card.dataset.location || '',
        time:        card.dataset.time || '',
        price:       card.dataset.price || '',
        date:        card.dataset.date || '',
    });

    const openModal = data => {
        lastFocusedElement = document.activeElement;
        modalImage.style.backgroundImage = data.image ? `url('${data.image}')` : 'none';
        activePosterImage = data.image || '';
        modalCategory.textContent = data.category || 'Event';
        modalTitle.textContent    = data.title    || 'Event Details';
        modalDate.textContent     = data.date     || 'Upcoming';
        modalTime.textContent     = data.time     || 'TBA';
        modalLocation.textContent = data.location || 'Venue details available';
        modalPrice.textContent    = data.price    || 'Check listing';
        modalDesc.textContent     = data.description || '';
        modalBuy.dataset.checkout = data.checkout || '';
        modalBuy.textContent = 'Get Tickets';
        modalArtists.innerHTML = '';
        (data.artists||[]).forEach(a => { const c=document.createElement('span'); c.className='artist-chip'; c.textContent=a; modalArtists.appendChild(c); });
        if (modalArtistsSec) modalArtistsSec.hidden = !(data.artists?.length);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden','false');
        document.body.classList.add('modal-open');
        modalCloseBtn?.focus();
    };
    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden','true');
        if (!posterModal || !posterModal.classList.contains('is-open')) {
            document.body.classList.remove('modal-open');
        }
        if (lastFocusedElement instanceof HTMLElement) {
            lastFocusedElement.focus();
        }
    };

    if (modalImage) {
        modalImage.addEventListener('click', () => openPosterModal(activePosterImage));
    }

    if (modalPosterBtn) {
        modalPosterBtn.addEventListener('click', () => openPosterModal(activePosterImage));
    }

    document.querySelectorAll('.hp-fcard,.hp-ecard').forEach(card => {
        card.setAttribute('tabindex','0');
        card.addEventListener('click', e => { if (e.target.closest('a')) return; openModal(dataFromCard(card)); });
        card.addEventListener('keydown', e => { if (e.key!=='Enter'&&e.key!==' ') return; e.preventDefault(); openModal(dataFromCard(card)); });
    });

    modal.querySelectorAll('[data-modal-close]').forEach(el => el.addEventListener('click', closeModal));
    document.addEventListener('keydown', e => {
        if (posterModal && posterModal.classList.contains('is-open')) {
            if (e.key === 'Escape') {
                closePosterModal();
                return;
            }
            trapFocusIn(e, posterModal);
            return;
        }

        if (modal.classList.contains('is-open')) {
            if (e.key === 'Escape') {
                closeModal();
                return;
            }
            trapFocusIn(e, modal);
        }
    });

    if (posterModal) {
        posterModal.querySelectorAll('[data-poster-close]').forEach(el => el.addEventListener('click', closePosterModal));
    }

    if (modalBuy) {
        modalBuy.addEventListener('click', () => {
            const checkoutUrl = modalBuy.dataset.checkout || '';

            if (checkoutUrl) {
                window.location.href = checkoutUrl;
            }
        });
    }
})();
</script>

<script>
(() => {
    const nowSec = Math.floor(Date.now() / 1000);

    document.querySelectorAll('.hp-fcard[data-ts], .hp-ecard[data-ts]').forEach(card => {
        const ts = parseInt(card.dataset.ts, 10);
        if (!ts || ts > nowSec) return;

        card.classList.add('is-past');

        // inject "Ended" badge into the image area
        const imgWrap = card.querySelector('.hp-fcard-img, .hp-ecard-thumb');
        if (imgWrap && !imgWrap.querySelector('.hp-past-badge')) {
            const badge = document.createElement('span');
            badge.className = 'hp-past-badge';
            badge.textContent = 'Ended';
            imgWrap.appendChild(badge);
        }
    });
})();
</script>

@endsection
