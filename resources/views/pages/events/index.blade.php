@extends('layouts.app')

@section('content')
    <section class="events-page">
        <div class="events-shell">

            <div class="events-head">
                <div>
                    <p class="eyebrow">Public listings</p>
                    <h1>{{ $activeCategoryLabel ? $activeCategoryLabel . ' Events' : 'Discover Events' }}</h1>
                </div>
                <div class="events-count">
                    {{ $events->count() }} {{ Str::plural('event', $events->count()) }}
                </div>
            </div>

            <form class="events-toolbar" action="{{ route('events.index') }}" method="GET">
                <label class="events-search">
                    <span class="sr-only">Search events</span>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search events, venues, cities">
                </label>
                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <button type="submit">Search</button>
            </form>

            @if ($categoryPills->isNotEmpty())
                <div class="events-category-bar" aria-label="Filter by category">
                    <a href="{{ route('events.index', array_filter(['search' => $search])) }}" class="category-pill {{ $activeCategory === '' ? 'is-active' : '' }}">
                        All
                    </a>
                    @foreach ($categoryPills as $categoryPill)
                        <a
                            href="{{ route('events.index', array_filter(['category' => $categoryPill['key'], 'search' => $search])) }}"
                            class="category-pill {{ $activeCategory === $categoryPill['key'] ? 'is-active' : '' }}"
                        >
                            {{ $categoryPill['label'] }}
                            <span>{{ $categoryPill['count'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="events-grid">
                @forelse ($events as $event)
                    @php
                        $isBookmarked = $bookmarkedIds->contains($event->id);
                        $liveStatus = $event->live_status;
                        $statusLabel = match ($liveStatus) {
                            'live' => 'Live now',
                            'ended' => 'Passed',
                            default => 'Upcoming',
                        };

                        $imagePath = trim((string) $event->image_url);
                        if ($imagePath === '') {
                            $imageUrl = asset('images/movie.jpg');
                        } elseif (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                            $imageUrl = $imagePath;
                        } else {
                            $normalizedImagePath = ltrim($imagePath, '/');

                            if (str_starts_with($normalizedImagePath, 'storage/') || str_starts_with($normalizedImagePath, 'images/')) {
                                $imageUrl = asset($normalizedImagePath);
                            } elseif (str_starts_with($normalizedImagePath, 'event-images/')) {
                                $imageUrl = asset('storage/' . $normalizedImagePath);
                            } else {
                                $imageUrl = asset($normalizedImagePath);
                            }
                        }
                    @endphp
                    <div class="event-card event-card--{{ $liveStatus }}">
                        <a href="{{ $event->url ?? route('events.show', $event) }}" class="event-card-img-link">
                            <div class="event-card-img" style="background-image: url('{{ $imageUrl }}')">
                                <span class="status-badge status-badge--{{ $liveStatus }}">{{ $statusLabel }}</span>
                                @if (in_array((string) ($event->service_package ?? 'online_ticketing'), ['batch_tickets', 'premium_wristbands'], true))
                                    <span class="price-badge paid">Buy at gate</span>
                                @elseif ((float) $event->ticket_price <= 0)
                                    <span class="price-badge free">Free</span>
                                @else
                                    <span class="price-badge paid">From UGX {{ number_format((float) $event->ticket_price, 0) }}</span>
                                @endif
                            </div>
                        </a>
                        <div class="event-card-body">
                            <div class="event-card-top">
                                <a href="{{ $event->url ?? route('events.show', $event) }}" class="event-title">{{ $event->title }}</a>
                                <button
                                    type="button"
                                    class="bookmark-btn {{ $isBookmarked ? 'is-bookmarked' : '' }}"
                                    data-event-id="{{ $event->id }}"
                                    data-bookmark-url="{{ auth()->check() ? route('events.bookmark', $event) : '' }}"
                                    data-login-url="{{ route('login') }}"
                                    aria-label="{{ $isBookmarked ? 'Remove bookmark' : 'Bookmark event' }}"
                                    title="{{ $isBookmarked ? 'Remove bookmark' : 'Save this event' }}"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                </button>
                            </div>
                            <ul class="event-meta">
                                <li class="meta-item">
                                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                                    {{ $event->starts_at->format('d M Y, h:i A') }}
                                </li>
                                <li class="meta-item">
                                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                                    {{ $event->venue }}{{ $event->city ? ', ' . $event->city : '' }}
                                </li>
                            </ul>
                            <div class="event-card-footer">
                                <span class="event-category">{{ $event->category_label }}</span>
                                <span class="event-state event-state--{{ $liveStatus }}">{{ $statusLabel }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:0.3;margin-bottom:1rem"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="#fff" stroke-width="1.2"/><path d="M5 2v2M11 2v2M2 7h12" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg>
                        <h2>No events found</h2>
                        <p>Try another category or search term.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <style>
        :root {
            --events-bg: #170b0f;
            --events-bg-2: #241017;
            --events-panel: #1f1116;
            --events-panel-soft: #25141a;
            --events-border: rgba(255, 255, 255, 0.14);
            --events-border-strong: rgba(192, 40, 60, 0.38);
            --events-text: #f7f2f4;
            --events-text-sub: rgba(247, 242, 244, 0.74);
            --events-text-muted: rgba(247, 242, 244, 0.56);
            --events-accent: #c0283c;
            --events-accent-dark: #9f2030;
            --events-blue: #1a73e8;
            --events-blue-dark: #145ec0;
        }

        /* ── Page shell ── */
        .events-page {
            padding: 9rem 1rem 5rem;
            background:
                radial-gradient(ellipse 70% 40% at 0% 0%, rgba(192, 40, 60, 0.16) 0%, transparent 62%),
                radial-gradient(ellipse 50% 28% at 100% 0%, rgba(26, 115, 232, 0.08) 0%, transparent 68%),
                linear-gradient(180deg, var(--events-bg) 0%, var(--events-bg-2) 100%);
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
            margin-bottom: 1.25rem;
        }
        .eyebrow {
            color: rgba(255, 216, 222, 0.85);
            font-size: 13px;
            margin: 0 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .events-head h1 {
            margin: 0;
            font-size: clamp(1.4rem, 2.5vw, 2rem);
            color: #fff;
            font-weight: 700;
        }
        .events-count {
            font-size: 13px;
            color: var(--events-text-sub);
            padding-bottom: 4px;
        }
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        .events-toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }
        .events-search {
            flex: 1;
        }
        .events-search input {
            width: 100%;
            height: 42px;
            border-radius: 8px;
            border: 1px solid var(--events-border);
            background: var(--events-panel);
            color: var(--events-text);
            padding: 0 14px;
            outline: none;
        }
        .events-search input::placeholder {
            color: var(--events-text-muted);
        }
        .events-search input:focus {
            border-color: var(--events-accent);
            box-shadow: 0 0 0 3px rgba(192, 40, 60, 0.16);
        }
        .events-toolbar button {
            height: 42px;
            border: 0;
            border-radius: 8px;
            background: var(--events-blue);
            color: #fff;
            font-weight: 700;
            padding: 0 18px;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .events-toolbar button:hover {
            background: var(--events-blue-dark);
        }
        .events-category-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 22px;
        }
        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 34px;
            padding: 7px 12px;
            border-radius: 999px;
            border: 1px solid var(--events-border);
            background: var(--events-panel);
            color: var(--events-text-sub);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }
        .category-pill span {
            color: var(--events-text-muted);
            font-size: 12px;
        }
        .category-pill:hover,
        .category-pill.is-active {
            border-color: var(--events-border-strong);
            color: #fff;
            background: rgba(192, 40, 60, 0.14);
        }

        /* ── Grid ── */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
        }

        /* ── Card ── */
        .event-card {
            position: relative;
            display: flex;
            flex-direction: column;
            background: var(--events-panel-soft);
            border: 1px solid var(--events-border);
            border-radius: 18px;
            overflow: hidden;
            transition: border-color 0.15s, transform 0.15s, box-shadow 0.15s;
        }
        .event-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 3px;
            background: transparent;
            z-index: 2;
        }
        .event-card:hover {
            border-color: rgba(192, 40, 60, 0.65);
            transform: translateY(-3px);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.32);
        }
        .event-card--live::before,
        .event-card--upcoming::before {
            background: linear-gradient(90deg, rgba(192, 40, 60, 0.95), rgba(26, 115, 232, 0.88));
        }
        .event-card--ended::before {
            background: rgba(148, 163, 184, 0.42);
        }
        .event-card--ended .event-card-img {
            filter: grayscale(56%) brightness(0.72);
        }
        .event-card-img-link { display: block; }

        /* ── Card image ── */
        .event-card-img {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background-size: cover;
            background-position: center;
            background-color: #2a1015;
        }
        .event-card-img::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.12) 0%, rgba(0, 0, 0, 0.44) 100%);
            pointer-events: none;
        }
        .status-badge,
        .price-badge {
            z-index: 1;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            left: 12px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .status-badge--live {
            background: rgba(192, 40, 60, 0.2);
            color: #ffc3cb;
            border: 1px solid rgba(255, 175, 185, 0.42);
        }
        .status-badge--upcoming {
            background: rgba(26, 115, 232, 0.16);
            color: #b9d8ff;
            border: 1px solid rgba(136, 187, 255, 0.35);
        }
        .status-badge--ended {
            background: rgba(17, 24, 39, 0.72);
            color: #d1d5db;
            border: 1px solid rgba(209, 213, 219, 0.22);
        }

        /* ── Price badge (overlaid on image) ── */
        .price-badge {
            position: absolute;
            bottom: 10px;
            left: 12px;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            letter-spacing: 0.01em;
        }
        .price-badge.free {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(110, 231, 183, 0.3);
        }
        .price-badge.paid {
            background: rgba(26, 115, 232, 0.2);
            color: #dbeafe;
            border: 1px solid rgba(147, 197, 253, 0.45);
        }

        /* ── Card body ── */
        .event-card-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        /* Keep subtle differentiation without bright slab */
        .event-card--live .event-card-body,
        .event-card--upcoming .event-card-body {
            background: linear-gradient(180deg, rgba(192, 40, 60, 0.08) 0%, rgba(37, 20, 26, 0.96) 100%);
            border-top: 1px solid rgba(192, 40, 60, 0.28);
        }

        .event-card--ended {
            border-color: rgba(148, 163, 184, 0.28);
            background: #1b171c;
        }

        .event-card--ended:hover {
            border-color: rgba(148, 163, 184, 0.42);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.24);
        }

        .event-card--ended .event-card-body {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.18) 0%, rgba(30, 24, 30, 0.94) 100%);
            border-top: 1px solid rgba(148, 163, 184, 0.2);
        }

        .event-card--ended .event-card-img::after {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.12) 0%, rgba(15, 23, 42, 0.42) 100%);
        }

        .event-card--ended .event-title,
        .event-card--ended .meta-item,
        .event-card--ended .event-category {
            color: rgba(226, 232, 240, 0.82);
        }

        .event-card--ended .event-title:hover {
            color: rgba(241, 245, 249, 0.96);
        }

        .event-card--ended .event-state {
            border: 1px solid rgba(148, 163, 184, 0.28);
        }

        .event-card--ended .price-badge {
            background: rgba(51, 65, 85, 0.55);
            color: rgba(226, 232, 240, 0.92);
            border-color: rgba(148, 163, 184, 0.35);
        }

        .event-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        /* ── Title ── */
        .event-title {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            line-height: 1.35;
            text-decoration: none;
            flex: 1;
        }
        .event-title:hover { color: #c0283c; }

        /* ── Bookmark button ── */
        .bookmark-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(247, 242, 244, 0.54);
            padding: 2px;
            flex-shrink: 0;
            line-height: 1;
            transition: color 0.15s, transform 0.15s;
        }
        .bookmark-btn:hover { color: #c0283c; transform: scale(1.15); }
        .bookmark-btn.is-bookmarked { color: #c0283c; }
        .bookmark-btn.is-bookmarked svg { fill: #c0283c; }

        /* ── Meta ── */
        .event-meta {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: auto;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--events-text-sub);
        }
        .meta-item svg {
            opacity: 0.68;
            flex-shrink: 0;
        }

        /* ── Empty state ── */
        .empty-state {
            grid-column: 1 / -1;
            background: var(--events-panel);
            border: 1px solid var(--events-border);
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            color: var(--events-text-sub);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .empty-state h2 {
            color: #fff;
            margin: 0 0 0.5rem;
            font-weight: 500;
            font-size: 18px;
        }
        .empty-state p {
            font-size: 14px;
        }

        /* ── Responsive ── */
        .event-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .event-category {
            color: var(--events-text-sub);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .event-state {
            font-size: 11px;
            font-weight: 800;
            border-radius: 999px;
            padding: 3px 8px;
            white-space: nowrap;
        }
        .event-state--live {
            color: #f87171;
            background: rgba(239, 68, 68, 0.12);
        }
        .event-state--upcoming {
            color: #b9d8ff;
            background: rgba(26, 115, 232, 0.16);
        }
        .event-state--ended {
            color: #d1d5db;
            background: rgba(107, 114, 128, 0.16);
        }

        .event-card-img-link:focus-visible,
        .event-title:focus-visible,
        .category-pill:focus-visible,
        .bookmark-btn:focus-visible,
        .events-toolbar button:focus-visible {
            outline: 2px solid rgba(26, 115, 232, 0.9);
            outline-offset: 2px;
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
            .events-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .events-toolbar {
                flex-direction: column;
            }
            .events-toolbar button {
                width: 100%;
            }
        }
    </style>

    <script>
    (() => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.bookmark-btn');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const bookmarkUrl = btn.dataset.bookmarkUrl;
            if (!bookmarkUrl) {
                window.location.href = btn.dataset.loginUrl + '?intended=' + encodeURIComponent(window.location.href);
                return;
            }

            fetch(bookmarkUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(r => r.json())
            .then(data => {
                const svg = btn.querySelector('svg');
                if (data.bookmarked) {
                    btn.classList.add('is-bookmarked');
                    btn.setAttribute('aria-label', 'Remove bookmark');
                    btn.title = 'Remove bookmark';
                    if (svg) svg.setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('is-bookmarked');
                    btn.setAttribute('aria-label', 'Bookmark event');
                    btn.title = 'Save this event';
                    if (svg) svg.setAttribute('fill', 'none');
                }
            });
        });
    })();
    </script>
@endsection
