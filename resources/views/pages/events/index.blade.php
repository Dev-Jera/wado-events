@extends('layouts.app')

@section('content')
    <section class="events-page">
        <div class="events-shell">

            <div class="events-head">
                <div>
                    <p class="eyebrow">Public listings</p>
                    <h1>Discover Events</h1>
                </div>
                <div class="events-count">
                    {{ $events->count() }} {{ Str::plural('event', $events->count()) }}
                </div>
            </div>

            <div class="events-grid">
                @forelse ($events as $event)
                    @php
                        $isBookmarked = $bookmarkedIds->contains($event->id);
                        $liveStatus = $event->live_status;
                    @endphp
                    <div class="event-card">
                        <a href="{{ $event->url ?? route('events.show', $event) }}" class="event-card-img-link">
                            <div class="event-card-img" style="background-image: url('{{ asset(ltrim((string) $event->image_url, '/')) }}')">
                                @if ($liveStatus === 'live')
                                    <span class="live-badge">● Live now</span>
                                @endif
                                @if ((float) $event->ticket_price <= 0)
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
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:0.3;margin-bottom:1rem"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="#fff" stroke-width="1.2"/><path d="M5 2v2M11 2v2M2 7h12" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg>
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
            padding: 9rem 1rem 5rem;
            background: linear-gradient(180deg, #150508 0%, #1e0b0e 100%);
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
            color: #6b7280;
            padding-bottom: 4px;
        }

        /* ── Grid ── */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        /* ── Card ── */
        .event-card {
            display: flex;
            flex-direction: column;
            background: #1a1a1a;
            border: 0.5px solid #2a2a2a;
            border-radius: 16px;
            overflow: hidden;
            transition: border-color 0.15s, transform 0.15s;
        }
        .event-card:hover {
            border-color: #c0283c;
            transform: translateY(-2px);
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

        /* ── Live badge ── */
        .live-badge {
            position: absolute;
            top: 10px;
            left: 12px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.18);
            color: #f87171;
            border: 0.5px solid rgba(248, 113, 113, 0.35);
            letter-spacing: 0.02em;
            animation: pulse-live 2s ease-in-out infinite;
        }
        @keyframes pulse-live {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.65; }
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
            border: 0.5px solid rgba(110, 231, 183, 0.3);
        }
        .price-badge.paid {
            background: rgba(192, 40, 60, 0.15);
            color: #c0283c;
            border: 0.5px solid rgba(192, 40, 60, 0.3);
        }

        /* ── Card body ── */
        .event-card-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
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
            font-size: 16px;
            font-weight: 600;
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
            color: #555;
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
            color: #888;
        }
        .meta-item svg {
            opacity: 0.5;
            flex-shrink: 0;
        }

        /* ── Empty state ── */
        .empty-state {
            grid-column: 1 / -1;
            background: #1a1a1a;
            border: 0.5px solid #2a2a2a;
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            color: #666;
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
        @media (max-width: 600px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
            .events-head {
                flex-direction: column;
                align-items: flex-start;
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