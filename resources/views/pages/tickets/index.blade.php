@extends('layouts.app')

@section('content')
@php
    $allTickets = $tickets->sortByDesc('purchased_at')->values();
    $upcoming = $tickets->whereIn('status', ['confirmed', 'active'])->sortBy('event.starts_at')->values();
    $attended = $tickets->where('status', 'used')->values();
    $cancelled = $tickets->where('status', 'cancelled')->values();
    $nextTicket = $upcoming->first();

    $year = now()->year;
    $bookedThisYear = $tickets->filter(fn ($t) => optional($t->purchased_at)?->year === $year)->count();
    $attendedThisYear = $attended->filter(fn ($t) => optional($t->event?->starts_at)?->year === $year)->count();
    $upcomingLeft = $upcoming->count();
    $totalSpent = $tickets->sum(fn ($t) => (float) $t->total_amount);

    $savedEvents = $upcoming->take(3);

    $recentActivities = $allTickets->take(8)->map(function ($ticket) {
        $state = $ticket->status === 'used' ? 'Used' : ($ticket->status === 'cancelled' ? 'Refunded' : 'Confirmed');
        $kind = $ticket->status === 'used' ? 'attended' : ($ticket->status === 'cancelled' ? 'cancelled' : 'booked');

        $headline = $ticket->status === 'used'
            ? 'Attended — ' . $ticket->event->title
            : ($ticket->status === 'cancelled'
                ? 'Cancelled — ' . $ticket->event->title
                : 'Ticket booked — ' . $ticket->event->title);

        $meta = $ticket->purchased_at->format('d M Y') . ' · '
            . ($ticket->status === 'cancelled'
                ? 'UGX ' . number_format((float) $ticket->total_amount, 0) . ' refunded'
                : 'Paid via ' . strtoupper((string) $ticket->payment_provider));

        return [
            'ticket' => $ticket,
            'headline' => $headline,
            'meta' => $meta,
            'state' => $state,
            'kind' => $kind,
        ];
    });
@endphp

<section class="td-page">
    <div class="td-shell">
        <section class="td-hero">
            <div class="td-hero-shape" aria-hidden="true"></div>
            <div class="td-hero-top">
                <div>
                    <p class="td-kicker">WELCOME BACK,</p>
                    <h1>{{ auth()->user()->name }}</h1>
                    <p class="td-sub">{{ now()->format('l, d F Y') }} · Kampala, UG</p>
                </div>
                <div class="td-hero-actions">
                    <a href="{{ route('events.index') }}">Browse events</a>
                    <button type="button">Download tickets</button>
                </div>
            </div>

            <div class="td-stat-grid">
                <article><strong>{{ $upcoming->count() }}</strong><span>Upcoming</span></article>
                <article><strong>{{ $tickets->sum('quantity') }}</strong><span>Total Tickets</span></article>
                <article><strong>{{ $attended->count() }}</strong><span>Attended</span></article>
                <article class="danger"><strong>{{ $cancelled->count() }}</strong><span>Cancelled</span></article>
            </div>

            <nav class="td-tabs" aria-label="Dashboard sections">
                <button class="on" type="button">Dashboard</button>
                <button type="button">My Tickets</button>
                <button type="button">Saved Events</button>
                <button type="button">Activity</button>
            </nav>
        </section>

        <section class="td-next-event">
            <p class="td-card-kicker">NEXT EVENT UP</p>
            @if ($nextTicket)
                @php
                    $daysLeft = max((int) now()->diffInDays($nextTicket->event->starts_at, false), 0);
                @endphp
                <div class="td-next-grid">
                    <div>
                        <h2>{{ $nextTicket->event->title }}</h2>
                        <div class="td-chip-row">
                            <span>{{ $nextTicket->event->starts_at->format('d M Y') }}</span>
                            <span>{{ $nextTicket->event->starts_at->format('H:i') }}</span>
                            <span>{{ $nextTicket->event->venue }}, {{ $nextTicket->event->city }}</span>
                            <span class="ok">Confirmed · {{ $nextTicket->ticketCategory->name }}</span>
                        </div>
                        <small>{{ $nextTicket->ticket_code }}</small>
                    </div>
                    <div class="td-next-right">
                        <div class="td-days"><strong>{{ $daysLeft }}</strong><span>DAYS LEFT</span></div>
                        <a href="{{ route('tickets.show', $nextTicket) }}" class="js-ticket-modal">View ticket</a>
                    </div>
                </div>
            @else
                <p class="td-empty-line">No upcoming events yet. <a href="{{ route('events.index') }}">Browse events</a></p>
            @endif
        </section>

        <section class="td-row-two">
            <article class="td-card">
                <header><h3>My tickets</h3><a href="#">View all →</a></header>
                <div class="td-list">
                    @forelse ($allTickets->take(4) as $ticket)
                        @php
                            $eventThumb = $ticket->event->image_url;
                            if ($eventThumb && !str_starts_with($eventThumb, 'http://') && !str_starts_with($eventThumb, 'https://') && !str_starts_with($eventThumb, '/')) {
                                $eventThumb = asset($eventThumb);
                            }
                            if (!$eventThumb) {
                                $eventThumb = asset('images/music.jpg');
                            }
                        @endphp
                        <a class="td-item td-ticket-row js-ticket-modal" href="{{ route('tickets.show', $ticket) }}">
                            <span class="thumb" style="background-image:url('{{ $eventThumb }}')" aria-hidden="true"></span>
                            <span class="main">
                                <strong>{{ \Illuminate\Support\Str::limit($ticket->event->title, 26) }}</strong>
                                <small>{{ $ticket->event->starts_at->format('d M') }} · {{ $ticket->event->venue }}</small>
                            </span>
                            <span class="state {{ $ticket->status === 'cancelled' ? 'bad' : ($ticket->status === 'used' ? 'gray' : 'ok') }}">{{ ucfirst((string) $ticket->status) }}</span>
                            <span class="chev">›</span>
                        </a>
                    @empty
                        <p class="td-empty-line">No tickets yet.</p>
                    @endforelse
                </div>
            </article>

            <div class="td-stack">
                <article class="td-card">
                    <header><h3>Your year in events</h3></header>
                    <div class="td-metrics">
                        <div><strong>{{ $bookedThisYear }}</strong><span>Events booked</span></div>
                        <div><strong>UGX {{ number_format($totalSpent, 0) }}</strong><span>Total spent</span></div>
                        <div><strong>{{ $attendedThisYear }}</strong><span>Events attended</span></div>
                        <div><strong>{{ $upcomingLeft }}</strong><span>Upcoming left</span></div>
                    </div>
                    <div class="td-progress-row">
                        <span>Events attended this year</span>
                        <strong>{{ $attendedThisYear }} / {{ max($bookedThisYear, 1) }}</strong>
                    </div>
                    <div class="td-progress"><i style="width: {{ min(100, (int) (($attendedThisYear / max($bookedThisYear, 1)) * 100)) }}%"></i></div>
                </article>

                <article class="td-offer">
                    <p>LIMITED OFFER</p>
                    <h4>Kampala Jazz Night — 30 Apr</h4>
                    <span>Early bird tickets available · Only 12 left</span>
                    <a href="{{ route('events.index') }}">Book now ↗</a>
                </article>
            </div>
        </section>

        <section class="td-card">
            <header><h3>Saved events</h3><a href="{{ route('events.index') }}">View all →</a></header>
            <div class="td-saved-grid">
                @forelse ($savedEvents as $ticket)
                    @php
                        $remaining = (int) ($ticket->event->tickets_available ?? 0);
                        $availability = $remaining <= 0 ? 'Sold out' : ($remaining <= 15 ? 'Limited' : 'Available');
                        $eventThumb = $ticket->event->image_url;
                        if ($eventThumb && !str_starts_with($eventThumb, 'http://') && !str_starts_with($eventThumb, 'https://') && !str_starts_with($eventThumb, '/')) {
                            $eventThumb = asset($eventThumb);
                        } elseif ($eventThumb && str_starts_with($eventThumb, '/')) {
                            $eventThumb = asset(ltrim($eventThumb, '/'));
                        }
                        if (!$eventThumb) {
                            $eventThumb = asset('images/movie.jpg');
                        }
                    @endphp
                    <a class="td-saved-card js-ticket-modal" href="{{ route('tickets.show', $ticket) }}" style="background-image:url('{{ $eventThumb }}')">
                        <em class="tag {{ str_replace(' ', '-', strtolower($availability)) }}">{{ $availability }}</em>
                        <span class="saved-main">
                            <strong>{{ $ticket->event->title }}</strong>
                            <small>{{ $ticket->event->starts_at->format('d M') }} · {{ $ticket->event->venue }}</small>
                        </span>
                    </a>
                @empty
                    <p class="td-empty-line">No saved events yet.</p>
                @endforelse
            </div>
        </section>

        <section class="td-card">
            <header><h3>Recent activity</h3><a href="#">View all →</a></header>
            <div class="td-list">
                @forelse ($recentActivities as $item)
                    @php
                        $eventThumb = $item['ticket']->event->image_url;
                        if ($eventThumb && !str_starts_with($eventThumb, 'http://') && !str_starts_with($eventThumb, 'https://') && !str_starts_with($eventThumb, '/')) {
                            $eventThumb = asset($eventThumb);
                        } elseif ($eventThumb && str_starts_with($eventThumb, '/')) {
                            $eventThumb = asset(ltrim($eventThumb, '/'));
                        }
                        if (!$eventThumb) {
                            $eventThumb = asset('images/music.jpg');
                        }
                    @endphp
                    <a class="td-item td-activity-row js-ticket-modal" href="{{ route('tickets.show', $item['ticket']) }}">
                        <span class="activity-icon {{ $item['kind'] }}" aria-hidden="true"></span>
                        <span class="main">
                            <strong>{{ $item['headline'] }}</strong>
                            <small>{{ $item['meta'] }}</small>
                        </span>
                        <span class="state {{ strtolower($item['state']) === 'confirmed' ? 'ok' : (strtolower($item['state']) === 'used' ? 'gray' : 'bad') }}">{{ $item['state'] }}</span>
                    </a>
                @empty
                    <p class="td-empty-line">No activity yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div id="ticket-modal" class="tk-modal" aria-hidden="true">
        <div class="tk-modal-backdrop" data-close-modal="1"></div>
        <div class="tk-modal-panel" role="dialog" aria-modal="true" aria-label="Ticket details preview">
            <div class="tk-modal-head">
                <strong>Ticket Preview</strong>
                <button type="button" id="ticket-modal-close" aria-label="Close ticket preview">×</button>
            </div>
            <iframe id="ticket-modal-frame" title="Ticket details"></iframe>
        </div>
    </div>
</section>

<style>
.td-page { min-height: 100vh; background: #eef1f7; padding: 7.8rem 0 2.2rem; font-family: var(--site-font); }
.td-page button,
.td-page input,
.td-page select,
.td-page textarea { font-family: inherit; }
.td-shell { width: min(1180px, calc(100% - 1.4rem)); margin: 0 auto; display: grid; gap: 0.9rem; }

.td-hero {
    background: linear-gradient(120deg, #08173c 0%, #071432 52%, #061129 100%);
    background-size: cover;
    background-position: center;
    border-radius: 20px;
    color: #fff;
    padding: 1rem;
    position: relative;
    overflow: hidden;
}
.td-hero-shape { position: absolute; inset: -20% -10% auto auto; width: 420px; height: 280px; background: radial-gradient(circle, rgba(86, 133, 255, .22) 0%, rgba(255,255,255,0) 70%); pointer-events: none; }
.td-hero-top { display: flex; align-items: start; justify-content: space-between; gap: 0.8rem; }
.td-kicker { margin: 0; font-size: 0.73rem; letter-spacing: 0.09em; font-weight: 700; opacity: 0.72; }
.td-hero h1 { margin: 0.2rem 0 0; font-size: clamp(1.3rem, 3vw, 2rem); }
.td-sub { margin: 0.32rem 0 0; font-size: 0.82rem; color: rgba(236, 244, 255, 0.82); }

.td-hero-actions { display: flex; gap: 0.45rem; }
.td-hero-actions a,
.td-hero-actions button {
    border: 1px solid rgba(255,255,255,.35);
    color: rgba(241, 247, 255, .96);
    text-decoration: none;
    background: rgba(12, 28, 66, 0.38);
    padding: 0.52rem 0.85rem;
    border-radius: 12px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
}

.td-stat-grid { margin-top: 0.95rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.58rem; }
.td-stat-grid article { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.2); border-radius: 14px; padding: 0.72rem 0.82rem; display: grid; gap: 0.12rem; }
.td-stat-grid article:nth-child(3) { background: rgba(8, 132, 110, .22); border-color: rgba(60, 229, 184, .38); }
.td-stat-grid article.danger { background: rgba(129, 24, 54, .33); border-color: rgba(237, 82, 129, .32); }
.td-stat-grid strong { font-size: 1.7rem; line-height: 1; }
.td-stat-grid span { font-size: 0.67rem; letter-spacing: 0.09em; text-transform: uppercase; color: rgba(236,244,255,.8); font-weight: 600; }

.td-tabs { margin-top: 0.8rem; display: flex; gap: 0.3rem; }
.td-tabs button { border: 1px solid rgba(255,255,255,.12); border-radius: 10px; background: rgba(3, 10, 30, .62); color: rgba(229, 237, 255, .84); padding: 0.46rem 0.78rem; font-size: 0.76rem; cursor: pointer; }
.td-tabs .on { background: rgba(11, 23, 56, .95); color: #ffffff; border-color: rgba(255,255,255,.25); font-weight: 700; }

.td-card,
.td-next-event {
    border: 1px solid #d8deea;
    border-radius: 16px;
    background: #f8fafc;
    overflow: hidden;
}

.td-next-event {
    padding: 0.7rem;
    background: #c8cdd6;
    color: #fff;
}
.td-card-kicker { margin: 0; font-size: 0.7rem; letter-spacing: 0.11em; color: #ffc39e; font-weight: 700; }
.td-next-grid { margin-top: 0.5rem; display: flex; align-items: end; justify-content: space-between; gap: 1rem; border-radius: 12px; padding: 0.9rem; background-size: cover; background-position: center; }
.td-next-grid { background: linear-gradient(105deg, #2a2d35 0%, #5b5d63 45%, #8b8d91 100%); }
.td-next-grid h2 { margin: 0; font-size: 2rem; }
.td-chip-row { margin-top: 0.52rem; display: flex; gap: 0.4rem; flex-wrap: wrap; }
.td-chip-row span { background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); border-radius: 999px; padding: 0.24rem 0.58rem; font-size: 0.7rem; }
.td-chip-row .ok { background: #edf9f2; color: #127747; border-color: #c3e9d1; }
.td-next-grid small { margin-top: 0.62rem; display: block; font-family: 'Courier New', monospace; color: rgba(214,229,255,.86); }
.td-next-right { display: grid; justify-items: end; gap: 0.55rem; }
.td-days { text-align: center; border: 1px solid rgba(255,255,255,.3); border-radius: 14px; background: rgba(255,255,255,.14); padding: 0.45rem 0.7rem; }
.td-days strong { display: block; font-size: 2rem; line-height: 1; }
.td-days span { font-size: 0.64rem; letter-spacing: 0.08em; }
.td-next-right a { text-decoration: none; color: #0f1a2d; background: rgba(247, 250, 255, .86); font-weight: 700; border: 1px solid rgba(255,255,255,.45); border-radius: 10px; padding: 0.44rem 0.76rem; }

.td-row-two { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; }
.td-stack { display: grid; gap: 0.82rem; }

.td-card header { padding: 0.86rem 0.95rem; border-bottom: 1px solid #e7edf7; display: flex; justify-content: space-between; align-items: center; }
.td-card header h3 { margin: 0; color: #142f54; }
.td-card header a { text-decoration: none; color: #2c69cf; font-size: 0.75rem; font-weight: 700; }

.td-card { background: #f8fafc; }

.td-list { padding: 0.48rem 0.7rem 0.75rem; display: grid; }
.td-item { display: grid; grid-template-columns: 34px 1fr auto; gap: 0.62rem; align-items: center; text-decoration: none; padding: 0.55rem 0.34rem; border-radius: 10px; }
.td-item:hover { background: #eef3fb; }
.td-item .icon { width: 34px; height: 34px; border-radius: 10px; background: #152f72; color: #fff; display: grid; place-items: center; font-size: 0.78rem; font-weight: 700; }
.td-item .thumb { width: 38px; height: 38px; border-radius: 8px; background-size: cover; background-position: center; display: block; border: 1px solid #d4dceb; }
.td-item .main strong { display: block; color: #132f54; font-size: 0.83rem; }
.td-item .main small { color: #111827; font-size: 0.72rem; }
.td-item .state { border-radius: 999px; padding: 0.2rem 0.52rem; border: 1px solid; font-size: 0.64rem; font-weight: 700; }
.td-item .state.ok { background: #edf9f2; color: #14784b; border-color: #bde8cb; }
.td-item .state.gray { background: #f2f6fc; color: #627a9b; border-color: #d6e2f2; }
.td-item .state.bad { background: #fff1f3; color: #9e2034; border-color: #f3c5cc; }

.td-ticket-row { grid-template-columns: 38px 1fr auto 16px; }
.td-ticket-row .chev { color: #111827; font-size: 1.2rem; line-height: 1; }

.td-metrics { padding: 0.2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-bottom: 1px solid #e4eaf3; }
.td-metrics div { padding: 0.7rem 0.9rem; border-right: 1px solid #e4eaf3; border-bottom: 1px solid #e4eaf3; }
.td-metrics div:nth-child(2n) { border-right: 0; }
.td-metrics div:nth-child(3),
.td-metrics div:nth-child(4) { border-bottom: 0; }
.td-metrics strong { display: block; font-size: 1.05rem; color: #102f56; }
.td-metrics span { font-size: 0.73rem; color: #111827; }
.td-progress-row { padding: 0 0.95rem; display: flex; justify-content: space-between; color: #111827; font-size: 0.75rem; font-weight: 600; }
.td-progress { margin: 0.45rem 0.95rem 0.95rem; height: 7px; border-radius: 999px; background: #ebf1f9; overflow: hidden; }
.td-progress i { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #5f8ddd, #7aa6ef); }

.td-offer { border: 1px solid #3d4452; border-radius: 16px; background: linear-gradient(130deg, #23262f 0%, #505463 100%); color: #fff; padding: 1rem; position: relative; overflow: hidden; display: grid; grid-template-columns: 1fr auto; gap: 0.6rem; align-items: center; }
.td-offer::after { content: ''; position: absolute; width: 220px; height: 220px; right: -60px; top: -90px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.16), rgba(255,255,255,0)); }
.td-offer p, .td-offer span { margin: 0; position: relative; z-index: 1; }
.td-offer p { font-size: 0.7rem; letter-spacing: 0.11em; color: rgba(224,237,255,.75); font-weight: 700; }
.td-offer h4 { margin: 0.28rem 0 0.18rem; position: relative; z-index: 1; font-size: 1.15rem; }
.td-offer span { color: rgba(220,236,255,.8); font-size: 0.77rem; }
.td-offer a { position: relative; z-index: 1; display: inline-block; text-decoration: none; color: #0f1729; background: #e6eaef; border: 1px solid rgba(255,255,255,.35); border-radius: 10px; padding: 0.58rem 0.94rem; font-weight: 700; }

.td-saved-grid { padding: 0.72rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.6rem; }
.td-saved-card { text-decoration: none; min-height: 128px; border-radius: 12px; border: 1px solid #dbe2ee; background-size: cover; background-position: center; position: relative; overflow: hidden; display: block; }
.td-saved-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(18, 23, 35, 0.06) 20%, rgba(17, 23, 35, 0.82) 100%); }
.td-saved-card .saved-main { position: absolute; left: 0.68rem; right: 0.68rem; bottom: 0.66rem; z-index: 2; }
.saved-main strong { display: block; font-size: 0.83rem; color: #ffffff; text-shadow: 0 2px 8px rgba(0,0,0,0.35); }
.saved-main small { color: rgba(241, 246, 255, 0.9); font-size: 0.71rem; text-shadow: 0 2px 8px rgba(0,0,0,0.35); }
.tag { border: 1px solid; border-radius: 999px; padding: 0.18rem 0.45rem; font-size: 0.62rem; font-weight: 700; font-style: normal; }
.td-saved-card .tag { position: absolute; right: 0.52rem; top: 0.52rem; z-index: 2; }
.tag.available { background: #edf9f2; color: #15754a; border-color: #bde8cb; }
.tag.limited { background: #fff6eb; color: #93540d; border-color: #ffd7a8; }
.tag.sold-out { background: #fff1f3; color: #9e2034; border-color: #f3c5cc; }

.td-activity-row { grid-template-columns: 24px 1fr auto; gap: 0.7rem; }
.activity-icon { width: 24px; height: 24px; border-radius: 8px; border: 1px solid #c7d6ef; background: #eaf2ff; position: relative; }
.activity-icon::before { content: ''; width: 8px; height: 5px; border: 1.5px solid #3d6cbe; border-radius: 2px; position: absolute; top: 8px; left: 7px; }
.activity-icon.cancelled { background: #fff2e8; border-color: #f2d2b0; }
.activity-icon.cancelled::before { border-color: #be6c3d; }
.activity-icon.attended { background: #eee8ff; border-color: #d8c7ff; }
.activity-icon.attended::before { border-color: #6849c5; }

.td-empty-line { margin: 0.35rem 0 0; color: #6f84a2; font-size: 0.78rem; }

.tk-modal { position: fixed; inset: 0; z-index: 1200; display: none; }
.tk-modal.open { display: block; }
.tk-modal-backdrop { position: absolute; inset: 0; background: rgba(10, 18, 34, 0.7); backdrop-filter: blur(4px); }
.tk-modal-panel { position: relative; width: min(1120px, calc(100% - 2rem)); height: min(86vh, 860px); margin: 4vh auto 0; background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #d8e1ef; box-shadow: 0 32px 70px rgba(7, 18, 36, 0.45); display: grid; grid-template-rows: 52px 1fr; }
.tk-modal-head { height: 52px; display: flex; justify-content: space-between; align-items: center; padding: 0 0.95rem; background: #f5f9ff; border-bottom: 1px solid #deebfb; }
.tk-modal-head strong { color: #1f3657; font-size: 0.86rem; letter-spacing: 0.02em; }
#ticket-modal-close { width: 30px; height: 30px; border: 0; border-radius: 10px; background: #e4edf9; color: #29466f; font-size: 1.1rem; cursor: pointer; }
#ticket-modal-frame { width: 100%; height: 100%; border: 0; }

@media (max-width: 1020px) {
    .td-row-two { grid-template-columns: 1fr; }
    .td-saved-grid { grid-template-columns: 1fr; }
}

@media (max-width: 700px) {
    .td-hero-top { flex-direction: column; }
    .td-hero-actions { width: 100%; }
    .td-hero-actions a, .td-hero-actions button { flex: 1; text-align: center; }
    .td-stat-grid { grid-template-columns: 1fr 1fr; }
    .td-tabs { overflow: auto; }
    .td-next-grid { flex-direction: column; align-items: start; }
    .td-next-right { width: 100%; justify-items: start; }
    .tk-modal-panel { width: calc(100% - 0.7rem); margin-top: 1.8vh; height: 92vh; }
}
</style>

<script>
const ticketModal = document.getElementById('ticket-modal');
const ticketModalFrame = document.getElementById('ticket-modal-frame');
const ticketModalClose = document.getElementById('ticket-modal-close');

function openTicketModal(url) {
    if (!ticketModal || !ticketModalFrame) return;
    const target = new URL(url, window.location.origin);
    target.searchParams.set('modal', '1');
    ticketModalFrame.src = target.toString();
    ticketModal.classList.add('open');
    ticketModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeTicketModal() {
    if (!ticketModal || !ticketModalFrame) return;
    ticketModal.classList.remove('open');
    ticketModal.setAttribute('aria-hidden', 'true');
    ticketModalFrame.src = 'about:blank';
    document.body.style.overflow = '';
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('a.js-ticket-modal');
    if (link) {
        event.preventDefault();
        openTicketModal(link.href);
        return;
    }

    const closer = event.target.closest('[data-close-modal="1"]');
    if (closer) {
        closeTicketModal();
    }
});

if (ticketModalClose) {
    ticketModalClose.addEventListener('click', closeTicketModal);
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeTicketModal();
    }
});
</script>
@endsection
