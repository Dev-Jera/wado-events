@extends('layouts.app')

@section('content')
@php
    $allTickets = $tickets->sortByDesc('purchased_at')->values();

    $upcoming = $tickets->filter(function ($t) {
        if (! in_array($t->status, [\App\Models\Ticket::STATUS_CONFIRMED, 'active'])) return false;
        if ($t->dismissed_at) return false;
        return in_array($t->event?->live_status ?? 'upcoming', ['upcoming', 'live']);
    })->sortBy('event.starts_at')->values();

    $attended  = $tickets->where('status', \App\Models\Ticket::STATUS_USED)->values();
    $cancelled = $tickets->where('status', \App\Models\Ticket::STATUS_CANCELLED)->values();

    $pastTickets = $tickets->filter(function ($t) {
        if ($t->dismissed_at) return false;
        $phase = $t->event?->live_status ?? 'upcoming';
        return $phase === 'ended' || $t->status === \App\Models\Ticket::STATUS_USED;
    })->sortByDesc('event.starts_at')->values();

    $refundRequestable = $allTickets->filter(function ($t) {
        $payment = $t->paymentTransaction;

        if (! $payment) return false;
        if ($t->dismissed_at || $t->used_at || $t->status === \App\Models\Ticket::STATUS_USED || $t->status === \App\Models\Ticket::STATUS_CANCELLED) return false;
        if ($payment->refund_requested_at || $payment->status === \App\Models\PaymentTransaction::STATUS_REFUNDED) return false;

        return in_array($payment->status, [
            \App\Models\PaymentTransaction::STATUS_CONFIRMED,
            \App\Models\PaymentTransaction::STATUS_PENDING,
            \App\Models\PaymentTransaction::STATUS_INITIATED,
        ], true);
    })->values();

    $refundRequested = $allTickets->filter(function ($t) {
        return (bool) ($t->paymentTransaction?->refund_requested_at)
            && $t->paymentTransaction?->status !== \App\Models\PaymentTransaction::STATUS_REFUNDED;
    })->values();

    $nextTicket      = $upcoming->first();
    $year            = now()->year;
    $bookedThisYear  = $tickets->filter(fn($t) => optional($t->purchased_at)?->year === $year)->count();
    $attendedThisYear = $attended->filter(fn($t) => optional($t->event?->starts_at)?->year === $year)->count();
    $upcomingLeft    = $upcoming->count();
    $totalSpent      = $tickets->sum(fn($t) => (float) $t->total_amount);

    $recentActivities = $allTickets->take(10)->map(function ($ticket) {
        $state = $ticket->status === \App\Models\Ticket::STATUS_USED ? 'Attended' : ($ticket->status === \App\Models\Ticket::STATUS_CANCELLED ? 'Cancelled' : 'Confirmed');
        $kind  = $ticket->status === \App\Models\Ticket::STATUS_USED ? 'attended' : ($ticket->status === \App\Models\Ticket::STATUS_CANCELLED ? 'cancelled' : 'booked');
        $headline = $ticket->status === \App\Models\Ticket::STATUS_USED
            ? 'Attended · ' . $ticket->event->title
            : ($ticket->status === \App\Models\Ticket::STATUS_CANCELLED ? 'Cancelled · ' . $ticket->event->title : 'Booked · ' . $ticket->event->title);
        $meta = $ticket->purchased_at->format('d M Y') . ' · '
            . ($ticket->status === \App\Models\Ticket::STATUS_CANCELLED
                ? 'UGX ' . number_format((float) $ticket->total_amount, 0) . ' refunded'
                : 'Paid via ' . strtoupper((string) $ticket->payment_provider));
        return compact('ticket', 'headline', 'meta', 'state', 'kind');
    });

    function thumbUrl($imageUrl, $fallback = 'images/music.jpg') {
        if (!$imageUrl) return asset($fallback);
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) return $imageUrl;
        return asset(ltrim($imageUrl, '/'));
    }
@endphp

<div class="db-page">

    {{-- ══════════════════════════════════════════
         HERO
    ══════════════════════════════════════════ --}}
    <header class="db-hero">
        <div class="db-hero-glow" aria-hidden="true"></div>
        <div class="db-hero-inner">
            <div class="db-hero-left">
                <p class="db-eyebrow">MY DASHBOARD</p>
                <h1>{{ auth()->user()->name }}</h1>
                <p class="db-date">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="db-hero-right">
                <a href="{{ route('events.index') }}" class="db-btn db-btn-outline">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Browse events
                </a>
            </div>
        </div>

        <div class="db-stats">
            <div class="db-stat">
                <strong>{{ $upcomingLeft }}</strong>
                <span>Upcoming</span>
            </div>
            <div class="db-stat-div"></div>
            <div class="db-stat">
                <strong>{{ $tickets->sum('quantity') }}</strong>
                <span>Total tickets</span>
            </div>
            <div class="db-stat-div"></div>
            <div class="db-stat db-stat-green">
                <strong>{{ $attended->count() }}</strong>
                <span>Attended</span>
            </div>
            <div class="db-stat-div"></div>
            <div class="db-stat db-stat-red">
                <strong>{{ $cancelled->count() }}</strong>
                <span>Cancelled</span>
            </div>
        </div>

        <nav class="db-tabs" id="db-tabs">
            <button class="db-tab active" data-tab="dashboard" type="button">Dashboard</button>
            <button class="db-tab" data-tab="tickets" type="button">
                My Tickets
                @if ($allTickets->count()) <em>{{ $allTickets->count() }}</em> @endif
            </button>
            <button class="db-tab" data-tab="saved" type="button">
                Saved
                @if ($bookmarkedEvents->count()) <em>{{ $bookmarkedEvents->count() }}</em> @endif
            </button>
            <button class="db-tab" data-tab="activity" type="button">Activity</button>
        </nav>
    </header>

    <div class="db-body">

        {{-- ══════════════════════════════════════════
             TAB: DASHBOARD
        ══════════════════════════════════════════ --}}
        <div class="db-panel active" id="tab-dashboard">

            {{-- Next event --}}
            @if ($nextTicket)
                @php
                    $daysLeft = max((int) now()->diffInDays($nextTicket->event->starts_at, false), 0);
                    $phase    = $nextTicket->event->live_status;
                    $bg       = thumbUrl($nextTicket->event->image_url ?? null);
                @endphp
                <div class="db-next" style="--bg: url('{{ $bg }}')">
                    <div class="db-next-overlay"></div>
                    <div class="db-next-inner">
                        <div class="db-next-left">
                            <p class="db-next-label">
                                @if ($phase === 'live')
                                    <span class="db-live-dot"></span> HAPPENING NOW
                                @else
                                    NEXT EVENT UP
                                @endif
                            </p>
                            <h2>{{ $nextTicket->event->title }}</h2>
                            <div class="db-chips">
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    {{ $nextTicket->event->starts_at->format('d M Y · H:i') }}
                                </span>
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                    {{ $nextTicket->event->venue }}, {{ $nextTicket->event->city }}
                                </span>
                                <span class="db-chip-confirmed">
                                    <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ $nextTicket->ticketCategory->name }}
                                </span>
                            </div>
                            <p class="db-next-code">{{ $nextTicket->ticket_code }}</p>
                        </div>
                        <div class="db-next-right">
                            @if ($phase === 'live')
                                <div class="db-days db-days-live">
                                    <strong>LIVE</strong>
                                </div>
                            @else
                                <div class="db-days">
                                    <strong>{{ $daysLeft }}</strong>
                                    <span>days left</span>
                                </div>
                            @endif
                            <a href="{{ route('tickets.show', $nextTicket) }}" class="db-btn db-btn-solid js-ticket-modal">View ticket</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="db-next-empty">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.35"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
                    <p>No upcoming events. <a href="{{ route('events.index') }}">Browse events →</a></p>
                </div>
            @endif

            {{-- Two-col: recent tickets + year stats --}}
            <div class="db-two-col">

                <div class="db-card">
                    <div class="db-card-head">
                        <h3>Recent tickets</h3>
                        <button type="button" class="db-link" data-tab-trigger="tickets">View all →</button>
                    </div>
                    @forelse ($allTickets->take(5) as $ticket)
                        @php $th = thumbUrl($ticket->event->image_url ?? null) @endphp
                        <a class="db-ticket-row js-ticket-modal" href="{{ route('tickets.show', $ticket) }}">
                            <span class="db-thumb" style="background-image:url('{{ $th }}')"></span>
                            <span class="db-row-main">
                                <strong>{{ \Illuminate\Support\Str::limit($ticket->event->title, 30) }}</strong>
                                <small>{{ $ticket->event->starts_at->format('d M Y') }} · {{ $ticket->ticketCategory->name }}</small>
                            </span>
                            <span class="db-pill {{ $ticket->status === \App\Models\Ticket::STATUS_CANCELLED ? 'pill-red' : ($ticket->status === \App\Models\Ticket::STATUS_USED ? 'pill-gray' : 'pill-green') }}">
                                {{ ucfirst((string) $ticket->status) }}
                            </span>
                        </a>
                    @empty
                        <p class="db-empty">No tickets yet.</p>
                    @endforelse
                </div>

                <div class="db-card">
                    <div class="db-card-head">
                        <h3>{{ $year }} in numbers</h3>
                    </div>
                    <div class="db-metrics">
                        <div class="db-metric">
                            <strong>{{ $bookedThisYear }}</strong>
                            <span>Booked this year</span>
                        </div>
                        <div class="db-metric">
                            <strong>{{ $attendedThisYear }}</strong>
                            <span>Events attended</span>
                        </div>
                        <div class="db-metric db-metric-wide">
                            <strong>UGX {{ number_format($totalSpent, 0) }}</strong>
                            <span>Total spent</span>
                        </div>
                        <div class="db-metric">
                            <strong>{{ $upcomingLeft }}</strong>
                            <span>Upcoming left</span>
                        </div>
                    </div>
                    <div class="db-prog-label">
                        <span>Attendance rate {{ $year }}</span>
                        <strong>{{ $attendedThisYear }}/{{ max($bookedThisYear,1) }}</strong>
                    </div>
                    <div class="db-prog-bar">
                        <div class="db-prog-fill" style="width:{{ min(100,(int)(($attendedThisYear/max($bookedThisYear,1))*100)) }}%"></div>
                    </div>

                    {{-- Upcoming list inside stats card --}}
                    @if ($upcoming->count())
                        <div class="db-card-sub-head">Upcoming events</div>
                        @foreach ($upcoming->take(3) as $ut)
                            <a class="db-ticket-row js-ticket-modal" href="{{ route('tickets.show', $ut) }}">
                                <span class="db-thumb" style="background-image:url('{{ thumbUrl($ut->event->image_url ?? null) }}')"></span>
                                <span class="db-row-main">
                                    <strong>{{ \Illuminate\Support\Str::limit($ut->event->title, 28) }}</strong>
                                    <small>{{ $ut->event->starts_at->format('d M') }} · {{ $ut->event->venue }}</small>
                                </span>
                                <span class="db-pill pill-orange">
                                    @if($ut->event->live_status === 'live') Live @else {{ $ut->event->starts_at->diffForHumans() }} @endif
                                </span>
                            </a>
                        @endforeach
                    @endif
                </div>

            </div>{{-- /two-col --}}

        </div>{{-- /tab-dashboard --}}


        {{-- ══════════════════════════════════════════
             TAB: MY TICKETS
        ══════════════════════════════════════════ --}}
        <div class="db-panel" id="tab-tickets">

            @if ($refundRequestable->isNotEmpty() || $refundRequested->isNotEmpty())
                <div class="db-card db-refund-card">
                    <div class="db-card-head">
                        <h3>Refund Requests</h3>
                        <span class="db-muted">Request a refund with a reason. Admin will review from the Refunds dashboard.</span>
                    </div>

                    @foreach ($refundRequestable as $ticket)
                        <form method="POST" action="{{ route('tickets.refund.request', $ticket) }}" class="db-refund-form">
                            @csrf
                            <div class="db-refund-head">
                                <strong>{{ $ticket->event->title }}</strong>
                                <span>{{ $ticket->ticket_code }} · {{ $ticket->ticketCategory->name }}</span>
                            </div>
                            <textarea name="reason" rows="2" maxlength="500" required placeholder="Explain why you want a refund..."></textarea>
                            <button type="submit" class="db-btn db-btn-solid">Send refund request</button>
                        </form>
                    @endforeach

                    @foreach ($refundRequested as $ticket)
                        <div class="db-refund-sent-row">
                            <strong>{{ $ticket->event->title }}</strong>
                            <span>Request submitted: {{ optional($ticket->paymentTransaction->refund_requested_at)->format('d M Y H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @forelse ($allTickets as $ticket)
                @php
                    $th    = thumbUrl($ticket->event->image_url ?? null);
                    $phase = $ticket->event->live_status;
                @endphp
                <a class="db-ticket-card js-ticket-modal" href="{{ route('tickets.show', $ticket) }}">
                    <span class="db-ticket-card-img" style="background-image:url('{{ $th }}')">
                        @if ($phase === 'live')
                            <em class="db-live-badge">● Live</em>
                        @endif
                    </span>
                    <span class="db-ticket-card-body">
                        <span class="db-ticket-card-title">{{ $ticket->event->title }}</span>
                        <span class="db-ticket-card-meta">
                            <span>
                                <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                {{ $ticket->event->starts_at->format('d M Y · H:i') }}
                            </span>
                            <span>
                                <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                {{ $ticket->event->venue }}, {{ $ticket->event->city }}
                            </span>
                            <span>{{ $ticket->ticketCategory->name }} · Qty {{ $ticket->quantity }}</span>
                        </span>
                        <span class="db-ticket-card-footer">
                            <span class="db-ticket-code">{{ $ticket->ticket_code }}</span>
                            <span class="db-pill {{ $ticket->status === \App\Models\Ticket::STATUS_CANCELLED ? 'pill-red' : ($ticket->status === \App\Models\Ticket::STATUS_USED ? 'pill-gray' : 'pill-green') }}">
                                {{ ucfirst((string) $ticket->status) }}
                            </span>
                        </span>
                    </span>
                </a>
            @empty
                <div class="db-big-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.25"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
                    <p>You haven't booked any tickets yet.</p>
                    <a href="{{ route('events.index') }}" class="db-btn db-btn-solid">Browse events</a>
                </div>
            @endforelse

        </div>{{-- /tab-tickets --}}


        {{-- ══════════════════════════════════════════
             TAB: SAVED EVENTS
        ══════════════════════════════════════════ --}}
        <div class="db-panel" id="tab-saved">

            @if ($bookmarkedEvents->isNotEmpty())
                <div class="db-saved-grid">
                    @foreach ($bookmarkedEvents as $event)
                        @php
                            $remaining    = (int) ($event->tickets_available ?? 0);
                            $availability = $remaining <= 0 ? 'Sold out' : ($remaining <= 15 ? 'Limited' : 'Available');
                            $phase        = $event->live_status;
                            $bg           = thumbUrl($event->image_url ?? null, 'images/movie.jpg');
                        @endphp
                        <div class="db-saved-wrap">
                            <a class="db-saved-card" href="{{ route('events.show', $event) }}" style="background-image:url('{{ $bg }}')">
                                <span class="db-saved-overlay"></span>
                                <span class="db-saved-badge-row">
                                    @if ($phase === 'live')
                                        <em class="db-badge db-badge-live">● Live now</em>
                                    @elseif ($remaining <= 0)
                                        <em class="db-badge db-badge-red">Sold out</em>
                                    @elseif ($remaining <= 15)
                                        <em class="db-badge db-badge-orange">Limited</em>
                                    @else
                                        <em class="db-badge db-badge-green">Available</em>
                                    @endif
                                </span>
                                <span class="db-saved-info">
                                    <strong>{{ $event->title }}</strong>
                                    <small>
                                        <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                        {{ $event->starts_at->format('d M Y') }}
                                        &nbsp;·&nbsp;
                                        <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                        {{ $event->venue }}
                                    </small>
                                    @if ((float)($event->ticketCategories->min('price') ?? 0) <= 0)
                                        <em class="db-saved-price">Free</em>
                                    @else
                                        <em class="db-saved-price">From UGX {{ number_format((float)$event->ticketCategories->min('price'), 0) }}</em>
                                    @endif
                                </span>
                            </a>
                            <form method="POST" action="{{ route('events.bookmark', $event) }}" class="db-unbookmark-form">
                                @csrf
                                <button type="submit" class="db-unbookmark-btn" title="Remove bookmark">
                                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="db-big-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.25"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <p>No saved events yet.</p>
                    <a href="{{ route('events.index') }}" class="db-btn db-btn-solid">Browse &amp; bookmark events</a>
                </div>
            @endif

        </div>{{-- /tab-saved --}}


        {{-- ══════════════════════════════════════════
             TAB: ACTIVITY
        ══════════════════════════════════════════ --}}
        <div class="db-panel" id="tab-activity">

            {{-- Recent activity --}}
            <div class="db-card">
                <div class="db-card-head">
                    <h3>Recent activity</h3>
                </div>
                @forelse ($recentActivities as $item)
                    @php $th = thumbUrl($item['ticket']->event->image_url ?? null) @endphp
                    <a class="db-ticket-row js-ticket-modal" href="{{ route('tickets.show', $item['ticket']) }}">
                        <span class="db-activity-icon db-activity-{{ $item['kind'] }}">
                            @if ($item['kind'] === 'attended')
                                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @elseif ($item['kind'] === 'cancelled')
                                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            @else
                                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z" stroke="currentColor" stroke-width="1.3"/></svg>
                            @endif
                        </span>
                        <span class="db-row-main">
                            <strong>{{ $item['headline'] }}</strong>
                            <small>{{ $item['meta'] }}</small>
                        </span>
                        <span class="db-pill {{ $item['state'] === 'Confirmed' ? 'pill-green' : ($item['state'] === 'Attended' ? 'pill-gray' : 'pill-red') }}">
                            {{ $item['state'] }}
                        </span>
                    </a>
                @empty
                    <p class="db-empty">No activity yet.</p>
                @endforelse
            </div>

            {{-- Past events (dismissible) --}}
            @if ($pastTickets->isNotEmpty())
                <div class="db-card" style="margin-top:0.8rem">
                    <div class="db-card-head">
                        <h3 style="color:#6b829e">Past events</h3>
                        <span class="db-muted">{{ $pastTickets->count() }} {{ Str::plural('event', $pastTickets->count()) }} · dismiss to hide</span>
                    </div>
                    @foreach ($pastTickets as $ticket)
                        @php $th = thumbUrl($ticket->event->image_url ?? null) @endphp
                        <div class="db-ticket-row db-past-row">
                            <span class="db-thumb db-thumb-muted" style="background-image:url('{{ $th }}')"></span>
                            <span class="db-row-main">
                                <strong>{{ \Illuminate\Support\Str::limit($ticket->event->title, 32) }}</strong>
                                <small>{{ $ticket->event->starts_at->format('d M Y') }} · {{ $ticket->event->venue }}</small>
                            </span>
                            <span class="db-pill {{ $ticket->status === \App\Models\Ticket::STATUS_USED ? 'pill-gray' : 'pill-green' }}">
                                {{ $ticket->status === \App\Models\Ticket::STATUS_USED ? 'Attended' : 'Ended' }}
                            </span>
                            <form method="POST" action="{{ route('tickets.dismiss', $ticket) }}" style="display:contents">
                                @csrf
                                <button type="submit" class="db-dismiss-btn" title="Dismiss from view">
                                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>{{-- /tab-activity --}}

    </div>{{-- /db-body --}}

</div>{{-- /db-page --}}

{{-- Ticket modal --}}
<div id="ticket-modal" class="tk-modal" aria-hidden="true">
    <div class="tk-modal-backdrop" data-close-modal="1"></div>
    <div class="tk-modal-panel" role="dialog" aria-modal="true" aria-label="Ticket details">
        <div class="tk-modal-head">
            <strong>Ticket Preview</strong>
            <button type="button" id="ticket-modal-close" aria-label="Close">×</button>
        </div>
        <iframe id="ticket-modal-frame" title="Ticket details"></iframe>
    </div>
</div>

<style>
/* ═══════════════════════════════════════
   BASE
═══════════════════════════════════════ */
.db-page {
    min-height: 100vh;
    background: #150508;
    padding-top: 0;
    font-family: var(--site-font, system-ui, sans-serif);
    color: #e2ecf8;
}
*, *::before, *::after { box-sizing: border-box; }

/* ═══════════════════════════════════════
   HERO
═══════════════════════════════════════ */
.db-hero {
    background: linear-gradient(140deg, #1a0508 0%, #220b0e 60%, #150407 100%);
    border-bottom: 1px solid #3a1520;
    padding: 7rem 0 0;
    position: relative;
    overflow: hidden;
}
.db-hero-glow {
    position: absolute;
    top: -80px; right: -80px;
    width: 480px; height: 360px;
    background: radial-gradient(ellipse, rgba(26,115,232,.14) 0%, transparent 70%);
    pointer-events: none;
}
.db-hero-inner {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.5rem 2rem 0;
    max-width: 1200px;
    margin: 0 auto;
}
.db-eyebrow {
    margin: 0 0 4px;
    font-size: 0.68rem;
    letter-spacing: 0.12em;
    font-weight: 700;
    color: #60a5fa;
}
.db-hero h1 {
    margin: 0;
    font-size: clamp(1.6rem, 3.5vw, 2.4rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
}
.db-date {
    margin: 4px 0 0;
    font-size: 0.8rem;
    color: #4a6480;
}

/* Stats bar */
.db-stats {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 1.2rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
.db-stat { padding: 0 1.4rem; }
.db-stat:first-child { padding-left: 0; }
.db-stat strong { display: block; font-size: 1.6rem; font-weight: 800; color: #60a5fa; line-height: 1; }
.db-stat span { font-size: 0.68rem; color: #4a6480; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }
.db-stat-green strong { color: #34d399; }
.db-stat-red strong   { color: #f87171; }
.db-stat-div { width: 1px; height: 32px; background: #1a2d45; }

/* Tabs */
.db-tabs {
    display: flex;
    gap: 0;
    padding: 0 2rem;
    max-width: 1200px;
    margin: 0 auto;
    border-top: 1px solid #1a2d45;
    margin-top: 0.4rem;
}
.db-tab {
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    color: #4a6480;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.8rem 1.1rem;
    cursor: pointer;
    transition: color .15s, border-color .15s;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: -1px;
    font-family: inherit;
}
.db-tab:hover { color: #c0d0e8; }
.db-tab.active { color: #60a5fa; border-bottom-color: #60a5fa; }
.db-tab em {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    background: rgba(26,115,232,.18);
    color: #60a5fa;
    font-style: normal;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 0 5px;
}

/* ═══════════════════════════════════════
   BODY
═══════════════════════════════════════ */
.db-body {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.2rem 2rem 4rem;
}
.db-panel { display: none; }
.db-panel.active { display: block; }

/* ═══════════════════════════════════════
   NEXT EVENT CARD
═══════════════════════════════════════ */
.db-next {
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    min-height: 200px;
    margin-bottom: 1rem;
    border: 1px solid #1e3050;
    background: #0f1c2e var(--bg) center/cover no-repeat;
}
.db-next-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(100deg,
        rgba(5,12,26,.96) 0%,
        rgba(5,12,26,.85) 50%,
        rgba(5,12,26,.45) 100%);
}
.db-next-inner {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 1.6rem 2rem;
}
.db-next-label {
    margin: 0 0 0.6rem;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    color: #60a5fa;
    display: flex;
    align-items: center;
    gap: 6px;
}
.db-live-dot {
    display: inline-block;
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #f87171;
    animation: pulse-dot 1.4s ease-in-out infinite;
}
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
.db-next h2 { margin: 0; font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 800; color: #fff; line-height: 1.2; }
.db-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.7rem; }
.db-chips span {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14);
    border-radius: 999px; padding: 0.22rem 0.6rem;
    font-size: 0.7rem; color: #c0d4f0;
}
.db-chip-confirmed { background: rgba(52,211,153,.12) !important; border-color: rgba(52,211,153,.3) !important; color: #34d399 !important; }
.db-next-code { margin: 0.8rem 0 0; font-family: 'Courier New', monospace; font-size: 0.72rem; color: rgba(160,190,230,.6); letter-spacing: 0.06em; }

.db-next-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.7rem; flex-shrink: 0; }
.db-days {
    text-align: center;
    background: rgba(26,115,232,.12);
    border: 1px solid rgba(26,115,232,.3);
    border-radius: 14px;
    padding: 0.7rem 1.2rem;
    min-width: 80px;
}
.db-days strong { display: block; font-size: 2rem; line-height: 1; font-weight: 800; color: #60a5fa; }
.db-days span { font-size: 0.6rem; letter-spacing: 0.1em; color: #60a5fa; font-weight: 700; text-transform: uppercase; }
.db-days-live strong { font-size: 1rem; letter-spacing: 0.06em; }

.db-next-empty {
    border: 1px dashed #1e3050;
    border-radius: 18px;
    padding: 2.5rem;
    text-align: center;
    color: #4a6480;
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
}
.db-next-empty p { margin: 0; font-size: 0.88rem; }
.db-next-empty a { color: #60a5fa; text-decoration: none; }

/* ═══════════════════════════════════════
   BUTTONS
═══════════════════════════════════════ */
.db-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.52rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    cursor: pointer;
    border: none;
    font-family: inherit;
    transition: background .15s, color .15s;
}
.db-btn-solid { background: #1255c0; color: #ffffff; }
.db-btn-solid:hover { background: #0e3fa0; }
.db-btn-outline {
    background: rgba(18,85,192,.08);
    border: 1px solid rgba(18,85,192,.35);
    color: #7ab3f5;
}
.db-btn-outline:hover { background: rgba(18,85,192,.15); }

/* ═══════════════════════════════════════
   TWO-COL LAYOUT
═══════════════════════════════════════ */
.db-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* ═══════════════════════════════════════
   CARDS
═══════════════════════════════════════ */
.db-card {
    background: #0f1c2e;
    border: 1px solid #1e3050;
    border-radius: 16px;
    overflow: hidden;
}
.db-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1.1rem 0.7rem;
    border-bottom: 1px solid #1a2d45;
}
.db-card-head h3 { margin: 0; font-size: 0.88rem; font-weight: 700; color: #c0d4f0; }
.db-card-sub-head {
    padding: 0.6rem 1.1rem 0.3rem;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    color: #3a5474;
    text-transform: uppercase;
    border-top: 1px solid #1a2d45;
    margin-top: 0.3rem;
}
.db-link {
    background: none; border: none; cursor: pointer;
    color: #60a5fa; font-size: 0.74rem; font-weight: 700;
    font-family: inherit; padding: 0;
}
.db-link:hover { text-decoration: underline; }

/* ═══════════════════════════════════════
   TICKET ROWS (inside cards)
═══════════════════════════════════════ */
.db-ticket-row {
    display: grid;
    grid-template-columns: 40px 1fr auto;
    gap: 0.7rem;
    align-items: center;
    padding: 0.62rem 1.1rem;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid #111d2e;
    transition: background .12s;
}
.db-ticket-row:last-child { border-bottom: none; }
.db-ticket-row:hover { background: rgba(255,255,255,.03); }
.db-thumb {
    width: 40px; height: 40px;
    border-radius: 8px;
    background-size: cover;
    background-position: center;
    background-color: #1a2d45;
    flex-shrink: 0;
    display: block;
}
.db-thumb-muted { opacity: .5; }
.db-row-main strong { display: block; font-size: 0.83rem; color: #c8daf0; font-weight: 600; }
.db-row-main small  { display: block; font-size: 0.71rem; color: #3d5574; margin-top: 1px; }
.db-empty { padding: 1rem 1.1rem; font-size: 0.8rem; color: #3d5574; margin: 0; }

/* ═══════════════════════════════════════
   METRICS
═══════════════════════════════════════ */
.db-metrics {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid #1a2d45;
}
.db-metric { padding: 0.8rem 1.1rem; border-right: 1px solid #1a2d45; border-bottom: 1px solid #1a2d45; }
.db-metric:nth-child(2n) { border-right: none; }
.db-metric-wide { grid-column: span 2; border-right: none; }
.db-metric strong { display: block; font-size: 1.1rem; font-weight: 800; color: #60a5fa; }
.db-metric span   { font-size: 0.7rem; color: #3d5574; }
.db-prog-label {
    display: flex; justify-content: space-between;
    padding: 0.6rem 1.1rem 0.3rem;
    font-size: 0.72rem; color: #3d5574; font-weight: 600;
}
.db-prog-label strong { color: #6b829e; }
.db-prog-bar { margin: 0 1.1rem 1rem; height: 6px; background: #1a2d45; border-radius: 999px; overflow: hidden; }
.db-prog-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #c0283c, #8a1525); transition: width .4s; }

/* ═══════════════════════════════════════
   STATUS PILLS
═══════════════════════════════════════ */
.db-pill {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.64rem;
    font-weight: 700;
    border: 1px solid;
    white-space: nowrap;
}
.pill-green  { background: rgba(52,211,153,.12); color: #34d399; border-color: rgba(52,211,153,.3); }
.pill-gray   { background: rgba(100,130,160,.12); color: #6b829e; border-color: rgba(100,130,160,.3); }
.pill-red    { background: rgba(248,113,113,.12); color: #f87171; border-color: rgba(248,113,113,.3); }
.pill-orange { background: rgba(251,146,60,.12); color: #f97316; border-color: rgba(251,146,60,.3); }

/* ═══════════════════════════════════════
   MY TICKETS (full cards)
═══════════════════════════════════════ */
.db-ticket-card {
    display: grid;
    grid-template-columns: 100px 1fr;
    background: #0f1c2e;
    border: 1px solid #1e3050;
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    margin-bottom: 0.6rem;
    transition: border-color .15s, transform .15s;
}
.db-ticket-card:hover { border-color: rgba(192,40,60,.5); transform: translateY(-1px); }
.db-ticket-card-img {
    background-size: cover;
    background-position: center;
    background-color: #1a2d45;
    position: relative;
    min-height: 90px;
}
.db-live-badge {
    position: absolute; top: 8px; left: 8px;
    font-style: normal; font-size: 0.65rem; font-weight: 700;
    background: rgba(248,113,113,.2); color: #f87171;
    border: 1px solid rgba(248,113,113,.35); border-radius: 999px;
    padding: 2px 8px;
}
.db-ticket-card-body {
    padding: 0.9rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.db-ticket-card-title { font-size: 0.95rem; font-weight: 700; color: #d8ecff; line-height: 1.3; }
.db-ticket-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.db-ticket-card-meta span {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.7rem; color: #4a6480;
}
.db-ticket-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 2px;
}
.db-ticket-code { font-family: 'Courier New', monospace; font-size: 0.68rem; color: #2d4a68; letter-spacing: 0.05em; }

.db-big-empty {
    text-align: center;
    padding: 4rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8rem;
    color: #3d5574;
}
.db-big-empty p { margin: 0; font-size: 0.88rem; }

/* ═══════════════════════════════════════
   SAVED EVENTS
═══════════════════════════════════════ */
.db-saved-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 0.8rem;
}
.db-saved-wrap { position: relative; }
.db-saved-card {
    display: block;
    text-decoration: none;
    border-radius: 14px;
    overflow: hidden;
    min-height: 160px;
    background-size: cover;
    background-position: center;
    background-color: #1a2d45;
    position: relative;
    border: 1px solid #1e3050;
    transition: border-color .15s, transform .15s;
}
.db-saved-card:hover { border-color: rgba(192,40,60,.5); transform: translateY(-2px); }
.db-saved-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(5,12,26,.1) 20%, rgba(5,12,26,.88) 100%);
}
.db-saved-badge-row { position: absolute; top: 0.6rem; left: 0.6rem; z-index: 2; }
.db-badge {
    display: inline-block;
    font-style: normal; font-size: 0.62rem; font-weight: 700;
    padding: 3px 9px; border-radius: 999px; border: 1px solid;
}
.db-badge-green  { background: rgba(52,211,153,.15); color: #34d399; border-color: rgba(52,211,153,.3); }
.db-badge-orange { background: rgba(251,146,60,.15); color: #f97316; border-color: rgba(251,146,60,.3); }
.db-badge-red    { background: rgba(248,113,113,.15); color: #f87171; border-color: rgba(248,113,113,.3); }
.db-badge-live   { background: rgba(248,113,113,.18); color: #f87171; border-color: rgba(248,113,113,.35); animation: pulse-live 2s ease-in-out infinite; }
@keyframes pulse-live { 0%,100%{opacity:1} 50%{opacity:.6} }
.db-saved-info {
    position: absolute; left: 0.8rem; right: 0.8rem; bottom: 0.8rem; z-index: 2;
}
.db-saved-info strong { display: block; font-size: 0.88rem; color: #fff; font-weight: 700; text-shadow: 0 2px 6px rgba(0,0,0,.5); }
.db-saved-info small  { display: flex; align-items: center; gap: 3px; font-size: 0.68rem; color: rgba(200,220,255,.8); margin-top: 3px; }
.db-saved-price { display: block; margin-top: 5px; font-style: normal; font-size: 0.72rem; font-weight: 700; color: #60a5fa; }
.db-unbookmark-form { position: absolute; top: 0.5rem; right: 0.5rem; z-index: 3; }
.db-unbookmark-btn {
    width: 26px; height: 26px; border-radius: 50%;
    background: rgba(5,12,26,.7); border: 1px solid rgba(255,255,255,.15);
    color: rgba(200,220,255,.7); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .12s, color .12s;
}
.db-unbookmark-btn:hover { background: rgba(220,40,40,.8); color: #fff; border-color: transparent; }

/* ═══════════════════════════════════════
   ACTIVITY
═══════════════════════════════════════ */
.db-activity-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.db-activity-booked   { background: rgba(26,115,232,.12); color: #60a5fa; border: 1px solid rgba(26,115,232,.25); }

.db-refund-card { margin-bottom: 0.9rem; }
.db-refund-form {
    padding: 0.8rem 1.1rem;
    border-bottom: 1px solid #111d2e;
    display: grid;
    gap: 0.5rem;
}
.db-refund-form:last-child { border-bottom: none; }
.db-refund-head { display: grid; gap: 2px; }
.db-refund-head strong { color: #d8ecff; font-size: 0.82rem; font-weight: 700; }
.db-refund-head span { color: #6b829e; font-size: 0.69rem; }
.db-refund-form textarea {
    resize: vertical;
    min-height: 58px;
    border-radius: 10px;
    border: 1px solid #2b4160;
    background: #0b1627;
    color: #e2ecf8;
    padding: 0.55rem 0.68rem;
    font-size: 0.75rem;
    font-family: inherit;
}
.db-refund-form textarea:focus {
    outline: none;
    border-color: #c0283c;
    box-shadow: 0 0 0 3px rgba(192,40,60,.18);
}
.db-refund-sent-row {
    display: grid;
    gap: 2px;
    padding: 0.7rem 1.1rem;
    border-top: 1px solid #111d2e;
}
.db-refund-sent-row strong { color: #c8daf0; font-size: 0.8rem; }
.db-refund-sent-row span { color: #6b829e; font-size: 0.69rem; }
.db-activity-attended { background: rgba(52,211,153,.10);  color: #34d399; border: 1px solid rgba(52,211,153,.25); }
.db-activity-cancelled{ background: rgba(248,113,113,.10); color: #f87171; border: 1px solid rgba(248,113,113,.25); }

.db-past-row { grid-template-columns: 40px 1fr auto auto !important; }
.db-dismiss-btn {
    background: none; border: none; cursor: pointer;
    color: #2d4a68; padding: 6px 8px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    transition: background .12s, color .12s;
}
.db-dismiss-btn:hover { background: rgba(248,113,113,.12); color: #f87171; }
.db-muted { font-size: 0.7rem; color: #2d4a68; }

/* ═══════════════════════════════════════
   TICKET MODAL
═══════════════════════════════════════ */
.tk-modal { position: fixed; inset: 0; z-index: 1200; display: none; }
.tk-modal.open { display: block; }
.tk-modal-backdrop { position: absolute; inset: 0; background: rgba(3,8,20,.75); backdrop-filter: blur(6px); }
.tk-modal-panel {
    position: relative;
    width: min(1100px, calc(100% - 2rem));
    height: min(88vh, 860px);
    margin: 4vh auto 0;
    background: #0a1628;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #1e3050;
    box-shadow: 0 40px 80px rgba(0,0,0,.6);
    display: grid;
    grid-template-rows: 52px 1fr;
}
.tk-modal-head {
    height: 52px;
    display: flex; justify-content: space-between; align-items: center;
    padding: 0 1rem;
    background: #0f1c2e;
    border-bottom: 1px solid #1e3050;
}
.tk-modal-head strong { color: #c0d4f0; font-size: 0.84rem; }
#ticket-modal-close {
    width: 30px; height: 30px;
    border: 1px solid #1e3050; border-radius: 8px;
    background: #1a2d45; color: #6b829e;
    font-size: 1.1rem; cursor: pointer; line-height: 1;
    transition: background .12s, color .12s;
}
#ticket-modal-close:hover { background: rgba(248,113,113,.2); color: #f87171; border-color: rgba(248,113,113,.4); }
#ticket-modal-frame { width: 100%; height: 100%; border: 0; }

/* ═══════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════ */
@media (max-width: 900px) {
    .db-two-col { grid-template-columns: 1fr; }
    .db-saved-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .db-hero-inner { flex-direction: column; align-items: flex-start; padding: 1.2rem 1rem 0; }
    .db-stats { flex-wrap: wrap; gap: 0.8rem; padding: 1rem; }
    .db-stat-div { display: none; }
    .db-tabs { padding: 0 0.5rem; overflow-x: auto; }
    .db-body { padding: 1rem 1rem 3rem; }
    .db-next-inner { flex-direction: column; align-items: flex-start; }
    .db-next-right { flex-direction: row; width: 100%; justify-content: space-between; align-items: center; }
    .db-ticket-card { grid-template-columns: 80px 1fr; }
    .db-saved-grid { grid-template-columns: 1fr; }
    .tk-modal-panel { width: calc(100% - 0.8rem); margin-top: 2vh; height: 93vh; }
}
</style>

<script>
(() => {
    // Tab switching
    const tabs   = document.querySelectorAll('.db-tab');
    const panels = document.querySelectorAll('.db-panel');

    function activateTab(name) {
        tabs.forEach(t   => t.classList.toggle('active', t.dataset.tab === name));
        panels.forEach(p => p.classList.toggle('active', p.id === 'tab-' + name));
    }

    tabs.forEach(tab => tab.addEventListener('click', () => activateTab(tab.dataset.tab)));

    // "View all →" inside cards triggers tab switch
    document.querySelectorAll('[data-tab-trigger]').forEach(btn => {
        btn.addEventListener('click', () => activateTab(btn.dataset.tabTrigger));
    });

    // Ticket modal
    const modal      = document.getElementById('ticket-modal');
    const modalFrame = document.getElementById('ticket-modal-frame');
    const modalClose = document.getElementById('ticket-modal-close');

    function openModal(url) {
        const target = new URL(url, window.location.origin);
        target.searchParams.set('modal', '1');
        modalFrame.src = target.toString();
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        modalFrame.src = 'about:blank';
        document.body.style.overflow = '';
    }

    document.addEventListener('click', e => {
        if (e.target.closest('a.js-ticket-modal')) {
            e.preventDefault();
            openModal(e.target.closest('a').href);
        }
        if (e.target.closest('[data-close-modal]')) closeModal();
    });
    if (modalClose) modalClose.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    // Restore active tab from hash
    const hash = window.location.hash.replace('#', '');
    if (['dashboard','tickets','saved','activity'].includes(hash)) activateTab(hash);
})();
</script>
@endsection
