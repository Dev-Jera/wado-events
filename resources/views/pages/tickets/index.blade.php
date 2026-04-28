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

    $nextTicket = $upcoming->first();

    // Assign filter group to each ticket
    $ticketGroups = [];
    $groupCounts  = ['upcoming' => 0, 'past' => 0, 'cancelled' => 0];
    foreach ($allTickets as $ticket) {
        $phase  = $ticket->event?->live_status ?? 'upcoming';
        $status = $ticket->status;
        if ($status === \App\Models\Ticket::STATUS_CANCELLED) {
            $g = 'cancelled';
        } elseif ($status === \App\Models\Ticket::STATUS_USED || $phase === 'ended') {
            $g = 'past';
        } else {
            $g = 'upcoming';
        }
        $ticketGroups[$ticket->id] = $g;
        $groupCounts[$g]++;
    }

    function thumbUrl($imageUrl, $fallback = 'images/music.jpg') {
        if (! $imageUrl) return asset($fallback);
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) return $imageUrl;
        return asset(ltrim($imageUrl, '/'));
    }
@endphp

<div class="mt-page">

    {{-- ══ HEADER ══ --}}
    <header class="mt-header">
        <div class="mt-header-inner">
            <div class="mt-header-left">
                <h1>My Tickets @if ($allTickets->count()) <em>{{ $allTickets->count() }}</em> @endif</h1>
                @if ($nextTicket)
                    <p class="mt-header-sub">Next up: <span>{{ Str::limit($nextTicket->event->title, 42) }}</span> · {{ $nextTicket->event->starts_at->format('d M Y') }}</p>
                @else
                    <p class="mt-header-sub">Your event tickets in one place</p>
                @endif
            </div>
            <a href="{{ route('events.index') }}" class="mt-browse-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Browse events
            </a>
        </div>
    </header>

    <div class="mt-body">

        {{-- ══ NEXT EVENT STRIP ══ --}}
        @if ($nextTicket)
            @php
                $daysLeft  = max((int) now()->diffInDays($nextTicket->event->starts_at, false), 0);
                $nextPhase = $nextTicket->event->live_status;
                $nextBg    = thumbUrl($nextTicket->event->image_url ?? null);
            @endphp
            <a class="mt-next-strip js-ticket-modal" href="{{ route('tickets.show', $nextTicket) }}">
                <span class="mt-next-thumb" style="background-image:url('{{ $nextBg }}')">
                    @if ($nextPhase === 'live')
                        <span class="mt-live-dot"></span>
                    @endif
                </span>
                <span class="mt-next-content">
                    <span class="mt-next-label">
                        @if ($nextPhase === 'live') HAPPENING NOW @else UP NEXT @endif
                    </span>
                    <span class="mt-next-title">{{ $nextTicket->event->title }}</span>
                    <span class="mt-next-meta">
                        {{ $nextTicket->event->starts_at->format('d M Y · H:i') }}
                        &nbsp;·&nbsp;
                        {{ $nextTicket->event->venue }}, {{ $nextTicket->event->city }}
                        &nbsp;·&nbsp;
                        {{ $nextTicket->ticketCategory->name }}
                    </span>
                </span>
                <span class="mt-next-right">
                    @if ($nextPhase === 'live')
                        <span class="mt-live-chip">● LIVE</span>
                    @else
                        <span class="mt-countdown">
                            <strong>{{ $daysLeft }}</strong>
                            <small>days</small>
                        </span>
                    @endif
                    <span class="mt-next-cta">View ticket →</span>
                </span>
            </a>
        @endif

        {{-- ══ REFUND REQUESTS ══ --}}
        @if ($refundRequestable->isNotEmpty() || $refundRequested->isNotEmpty())
            <div class="mt-refund-card">
                <div class="mt-refund-head">
                    <h3>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12h18M3 6l9-3 9 3M3 18l9 3 9-3"/></svg>
                        Refund Requests
                    </h3>
                    <span>Submit a reason — our team will review your request.</span>
                </div>
                @foreach ($refundRequestable as $ticket)
                    <form method="POST" action="{{ route('tickets.refund.request', $ticket) }}" class="mt-refund-form">
                        @csrf
                        <div class="mt-refund-event">
                            <strong>{{ $ticket->event->title }}</strong>
                            <span>{{ $ticket->ticket_code }} · {{ $ticket->ticketCategory->name }}</span>
                        </div>
                        <textarea name="reason" rows="2" maxlength="500" required placeholder="Explain why you want a refund..."></textarea>
                        <button type="submit" class="mt-action-btn">Send refund request</button>
                    </form>
                @endforeach
                @foreach ($refundRequested as $ticket)
                    <div class="mt-refund-sent">
                        <strong>{{ $ticket->event->title }}</strong>
                        <span>Request submitted {{ optional($ticket->paymentTransaction->refund_requested_at)->format('d M Y · H:i') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ══ FILTER PILLS ══ --}}
        <div class="mt-filters">
            <button class="mt-filter active" data-f="all" type="button">All <em>{{ $allTickets->count() }}</em></button>
            <button class="mt-filter" data-f="upcoming" type="button">Upcoming <em>{{ $groupCounts['upcoming'] }}</em></button>
            <button class="mt-filter" data-f="past" type="button">Past <em>{{ $groupCounts['past'] }}</em></button>
            <button class="mt-filter" data-f="cancelled" type="button">Cancelled <em>{{ $groupCounts['cancelled'] }}</em></button>
        </div>

        {{-- ══ TICKET LIST ══ --}}
        <div class="mt-list" id="mt-list">
            @forelse ($allTickets as $ticket)
                @php
                    $th    = thumbUrl($ticket->event->image_url ?? null);
                    $phase = $ticket->event->live_status;
                    $group = $ticketGroups[$ticket->id];

                    $pillClass = match(true) {
                        $ticket->status === \App\Models\Ticket::STATUS_CANCELLED => 'pill-red',
                        $ticket->status === \App\Models\Ticket::STATUS_USED      => 'pill-gray',
                        $phase === 'live'                                         => 'pill-live',
                        $phase === 'ended'                                        => 'pill-gray',
                        default                                                   => 'pill-green',
                    };
                    $pillLabel = match(true) {
                        $ticket->status === \App\Models\Ticket::STATUS_CANCELLED => 'Cancelled',
                        $ticket->status === \App\Models\Ticket::STATUS_USED      => 'Attended',
                        $phase === 'live'                                         => '● Live',
                        $phase === 'ended'                                        => 'Ended',
                        default                                                   => 'Confirmed',
                    };
                @endphp
                <div class="mt-ticket-wrap" data-group="{{ $group }}">
                    <a class="mt-ticket js-ticket-modal" href="{{ route('tickets.show', $ticket) }}">
                        <span class="mt-ticket-img" style="background-image:url('{{ $th }}')">
                            @if ($phase === 'live')
                                <em class="mt-live-badge">● Live</em>
                            @endif
                            <em class="mt-status-stripe stripe-{{ $group }}"></em>
                        </span>
                        <span class="mt-ticket-body">
                            <span class="mt-ticket-top">
                                <span class="mt-ticket-title">{{ $ticket->event->title }}</span>
                                <span class="mt-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                            </span>
                            <span class="mt-ticket-meta">
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    {{ $ticket->event->starts_at->format('d M Y · H:i') }}
                                </span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                    {{ $ticket->event->venue }}, {{ $ticket->event->city }}
                                </span>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z" stroke="currentColor" stroke-width="1.3"/></svg>
                                    {{ $ticket->ticketCategory->name }} · Qty {{ $ticket->quantity }}
                                </span>
                            </span>
                            <span class="mt-ticket-foot">
                                <span class="mt-code">{{ $ticket->ticket_code }}</span>
                                <span class="mt-amount">UGX {{ number_format((float) $ticket->total_amount, 0) }}</span>
                            </span>
                        </span>
                    </a>
                    @if ($group === 'past')
                        <form method="POST" action="{{ route('tickets.dismiss', $ticket) }}" class="mt-dismiss-form">
                            @csrf
                            <button type="submit" class="mt-dismiss-btn" title="Dismiss from view">
                                <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="mt-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
                    <p>No tickets yet.</p>
                    <a href="{{ route('events.index') }}" class="mt-action-btn">Browse events</a>
                </div>
            @endforelse

            <p class="mt-no-match" style="display:none">No tickets match this filter.</p>
        </div>

        {{-- ══ SAVED EVENTS ══ --}}
        @if ($bookmarkedEvents->isNotEmpty())
            <section class="mt-saved">
                <h2 class="mt-section-title">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    Saved events
                    <em>{{ $bookmarkedEvents->count() }}</em>
                </h2>
                <div class="mt-saved-grid">
                    @foreach ($bookmarkedEvents as $event)
                        @php
                            $remaining = (int) ($event->tickets_available ?? 0);
                            $evPhase   = $event->live_status;
                            $evBg      = thumbUrl($event->image_url ?? null, 'images/movie.jpg');
                        @endphp
                        <div class="mt-saved-wrap">
                            <a class="mt-saved-card" href="{{ route('events.show', $event) }}" style="background-image:url('{{ $evBg }}')">
                                <span class="mt-saved-overlay"></span>
                                <span class="mt-saved-top">
                                    @if ($evPhase === 'live')
                                        <em class="mt-badge mt-badge-live">● Live</em>
                                    @elseif ($remaining <= 0)
                                        <em class="mt-badge mt-badge-red">Sold out</em>
                                    @elseif ($remaining <= 15)
                                        <em class="mt-badge mt-badge-orange">Limited</em>
                                    @else
                                        <em class="mt-badge mt-badge-green">Available</em>
                                    @endif
                                </span>
                                <span class="mt-saved-info">
                                    <strong>{{ $event->title }}</strong>
                                    <small>{{ $event->starts_at->format('d M Y') }} · {{ $event->venue }}</small>
                                    @if ((float) ($event->ticketCategories->min('price') ?? 0) <= 0)
                                        <em>Free</em>
                                    @else
                                        <em>From UGX {{ number_format((float) $event->ticketCategories->min('price'), 0) }}</em>
                                    @endif
                                </span>
                            </a>
                            <form method="POST" action="{{ route('events.bookmark', $event) }}" class="mt-unbookmark-form">
                                @csrf
                                <button type="submit" class="mt-unbookmark-btn" title="Remove bookmark">
                                    <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>{{-- /mt-body --}}
</div>{{-- /mt-page --}}

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
*, *::before, *::after { box-sizing: border-box; }

.mt-page {
    min-height: 100vh;
    background: #150508;
    font-family: var(--site-font, system-ui, sans-serif);
    color: #e2ecf8;
}

/* ═══════════════════════════════════════
   HEADER
═══════════════════════════════════════ */
.mt-header {
    padding-top: 7rem;
    padding-bottom: 1.6rem;
    background: linear-gradient(140deg, #1a0508 0%, #220b0e 60%, #150407 100%);
    border-bottom: 1px solid #3a1520;
}
.mt-header-inner {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
}
.mt-header-left h1 {
    margin: 0 0 4px;
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 10px;
}
.mt-header-left h1 em {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 26px;
    border-radius: 999px;
    background: rgba(18,85,192,.18);
    color: #7ab3f5;
    font-style: normal;
    font-size: 0.72rem;
    font-weight: 800;
    padding: 0 7px;
}
.mt-header-sub {
    margin: 0;
    font-size: 0.78rem;
    color: #7a5060;
}
.mt-header-sub span { color: #b08090; }
.mt-browse-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.52rem 1rem;
    border-radius: 10px;
    background: rgba(18,85,192,.1);
    border: 1px solid rgba(18,85,192,.3);
    color: #7ab3f5;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background .15s;
    flex-shrink: 0;
}
.mt-browse-btn:hover { background: rgba(18,85,192,.2); }

/* ═══════════════════════════════════════
   BODY
═══════════════════════════════════════ */
.mt-body {
    max-width: 900px;
    margin: 0 auto;
    padding: 1.4rem 1.5rem 5rem;
}

/* ═══════════════════════════════════════
   NEXT EVENT STRIP
═══════════════════════════════════════ */
.mt-next-strip {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(100deg, #0d1e38 0%, #0f2040 100%);
    border: 1px solid rgba(18,85,192,.35);
    border-left: 4px solid #1255c0;
    border-radius: 14px;
    padding: 0.9rem 1.1rem;
    text-decoration: none;
    margin-bottom: 1.2rem;
    transition: border-color .15s, transform .15s;
    overflow: hidden;
}
.mt-next-strip:hover { border-color: rgba(18,85,192,.6); transform: translateY(-1px); }
.mt-next-thumb {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    background-size: cover;
    background-position: center;
    background-color: #1a2d45;
    flex-shrink: 0;
    position: relative;
}
.mt-live-dot {
    position: absolute;
    top: -3px; right: -3px;
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #f87171;
    border: 2px solid #0d1e38;
    animation: pulse-dot 1.4s ease-in-out infinite;
}
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
.mt-next-content { flex: 1; min-width: 0; }
.mt-next-label {
    display: block;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: #7ab3f5;
    margin-bottom: 3px;
}
.mt-next-title {
    display: block;
    font-size: 0.92rem;
    font-weight: 700;
    color: #d8ecff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mt-next-meta {
    display: block;
    font-size: 0.7rem;
    color: #6080a0;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mt-next-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
}
.mt-countdown {
    text-align: center;
    background: rgba(18,85,192,.12);
    border: 1px solid rgba(18,85,192,.25);
    border-radius: 10px;
    padding: 0.3rem 0.7rem;
}
.mt-countdown strong { display: block; font-size: 1.3rem; font-weight: 800; color: #7ab3f5; line-height: 1; }
.mt-countdown small  { font-size: 0.58rem; color: #4a6480; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
.mt-live-chip {
    background: rgba(248,113,113,.15);
    color: #f87171;
    border: 1px solid rgba(248,113,113,.3);
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 3px 10px;
    animation: pulse-live 1.6s ease-in-out infinite;
}
@keyframes pulse-live { 0%,100%{opacity:1} 50%{opacity:.6} }
.mt-next-cta {
    font-size: 0.72rem;
    font-weight: 700;
    color: #7ab3f5;
}

/* ═══════════════════════════════════════
   REFUND CARD
═══════════════════════════════════════ */
.mt-refund-card {
    background: #0f1c2e;
    border: 1px solid #1e3050;
    border-left: 4px solid #f87171;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 1.2rem;
}
.mt-refund-head {
    padding: 0.9rem 1.1rem;
    border-bottom: 1px solid #1a2d45;
}
.mt-refund-head h3 {
    margin: 0 0 3px;
    font-size: 0.88rem;
    font-weight: 700;
    color: #c0d4f0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mt-refund-head span { font-size: 0.72rem; color: #7a5060; }
.mt-refund-form {
    padding: 0.9rem 1.1rem;
    border-bottom: 1px solid #111d2e;
    display: grid;
    gap: 0.5rem;
}
.mt-refund-form:last-of-type { border-bottom: none; }
.mt-refund-event strong { display: block; font-size: 0.82rem; color: #d8ecff; font-weight: 700; }
.mt-refund-event span   { font-size: 0.7rem; color: #7a5060; }
.mt-refund-form textarea {
    resize: vertical;
    min-height: 56px;
    border-radius: 10px;
    border: 1px solid #2b4160;
    background: #0b1627;
    color: #e2ecf8;
    padding: 0.55rem 0.7rem;
    font-size: 0.75rem;
    font-family: inherit;
    outline: none;
}
.mt-refund-form textarea:focus { border-color: #1255c0; box-shadow: 0 0 0 3px rgba(18,85,192,.15); }
.mt-refund-sent {
    padding: 0.7rem 1.1rem;
    border-top: 1px solid #111d2e;
    display: grid;
    gap: 2px;
}
.mt-refund-sent strong { color: #c8daf0; font-size: 0.8rem; font-weight: 600; }
.mt-refund-sent span   { color: #7a5060; font-size: 0.7rem; }

/* ═══════════════════════════════════════
   FILTER PILLS
═══════════════════════════════════════ */
.mt-filters {
    display: flex;
    gap: 0.4rem;
    margin-bottom: 0.9rem;
    flex-wrap: wrap;
}
.mt-filter {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 0.38rem 0.85rem;
    border-radius: 999px;
    border: 1px solid #3a1a22;
    background: transparent;
    color: #8a6070;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: background .12s, color .12s, border-color .12s;
}
.mt-filter em {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #2a0e14;
    color: #7a5060;
    font-style: normal;
    font-size: 0.62rem;
    font-weight: 800;
    padding: 0 4px;
    transition: background .12s, color .12s;
}
.mt-filter:hover { border-color: #5a2030; color: #c08090; }
.mt-filter.active {
    background: rgba(18,85,192,.12);
    border-color: rgba(18,85,192,.4);
    color: #7ab3f5;
}
.mt-filter.active em {
    background: rgba(18,85,192,.2);
    color: #7ab3f5;
}

/* ═══════════════════════════════════════
   TICKET LIST
═══════════════════════════════════════ */
.mt-list { display: flex; flex-direction: column; gap: 0.55rem; }

.mt-ticket-wrap {
    position: relative;
}
.mt-ticket-wrap[hidden] { display: none; }

.mt-ticket {
    display: grid;
    grid-template-columns: 120px 1fr;
    background: #0f1c2e;
    border: 1px solid #1a2d45;
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: border-color .15s, transform .15s;
    min-height: 110px;
}
.mt-ticket:hover { border-color: rgba(18,85,192,.45); transform: translateY(-1px); }

.mt-ticket-img {
    position: relative;
    background-size: cover;
    background-position: center;
    background-color: #1a2d45;
}
.mt-live-badge {
    position: absolute;
    top: 8px; left: 8px;
    font-style: normal;
    font-size: 0.62rem;
    font-weight: 700;
    background: rgba(248,113,113,.2);
    color: #f87171;
    border: 1px solid rgba(248,113,113,.35);
    border-radius: 999px;
    padding: 2px 7px;
    z-index: 1;
}
.mt-status-stripe {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
}
.stripe-upcoming { background: #1255c0; }
.stripe-past     { background: #3d5574; }
.stripe-cancelled{ background: #7f1d1d; }

.mt-ticket-body {
    padding: 0.9rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}
.mt-ticket-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
}
.mt-ticket-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #d8ecff;
    line-height: 1.3;
    flex: 1;
    min-width: 0;
}
.mt-ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem 0.8rem;
    flex: 1;
}
.mt-ticket-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.71rem;
    color: #5a7898;
}
.mt-ticket-foot {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 4px;
    border-top: 1px solid #111d2e;
}
.mt-code {
    font-family: 'Courier New', monospace;
    font-size: 0.68rem;
    color: #4a6890;
    letter-spacing: 0.05em;
}
.mt-amount {
    font-size: 0.72rem;
    font-weight: 700;
    color: #6080a0;
}

/* status pills */
.mt-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    font-size: 0.64rem;
    font-weight: 700;
    border: 1px solid;
    white-space: nowrap;
    flex-shrink: 0;
}
.pill-green  { background: rgba(52,211,153,.1);  color: #34d399; border-color: rgba(52,211,153,.28); }
.pill-gray   { background: rgba(100,130,160,.1); color: #6b829e; border-color: rgba(100,130,160,.28); }
.pill-red    { background: rgba(248,113,113,.1); color: #f87171; border-color: rgba(248,113,113,.28); }
.pill-live   { background: rgba(248,113,113,.15); color: #f87171; border-color: rgba(248,113,113,.3); animation: pulse-live 1.6s ease-in-out infinite; }

/* dismiss button */
.mt-dismiss-form {
    position: absolute;
    top: 8px; right: 8px;
    z-index: 2;
    display: contents;
}
.mt-dismiss-btn {
    position: absolute;
    top: 8px; right: 8px;
    width: 24px; height: 24px;
    border-radius: 50%;
    background: rgba(5,12,26,.7);
    border: 1px solid rgba(255,255,255,.1);
    color: rgba(160,190,230,.5);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: background .12s, color .12s;
}
.mt-dismiss-btn:hover { background: rgba(220,40,40,.7); color: #fff; border-color: transparent; }

/* no-match message */
.mt-no-match {
    text-align: center;
    padding: 2.5rem;
    font-size: 0.84rem;
    color: #8a6070;
}

/* empty state */
.mt-empty {
    text-align: center;
    padding: 4rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8rem;
    color: #8a6070;
}
.mt-empty p { margin: 0; font-size: 0.88rem; }

/* action button (refund submit / empty state browse) */
.mt-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.52rem 1.1rem;
    border-radius: 10px;
    background: #1255c0;
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: background .15s;
}
.mt-action-btn:hover { background: #0e3fa0; }

/* ═══════════════════════════════════════
   SAVED EVENTS
═══════════════════════════════════════ */
.mt-saved { margin-top: 2.5rem; }
.mt-section-title {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #8a6070;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin: 0 0 0.9rem;
}
.mt-section-title em {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #2a0e14;
    color: #7a5060;
    font-style: normal;
    font-size: 0.62rem;
    font-weight: 800;
    padding: 0 4px;
}
.mt-saved-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 0.7rem;
}
.mt-saved-wrap { position: relative; }
.mt-saved-card {
    display: block;
    text-decoration: none;
    border-radius: 12px;
    overflow: hidden;
    min-height: 150px;
    background-size: cover;
    background-position: center;
    background-color: #1a2d45;
    position: relative;
    border: 1px solid #1e3050;
    transition: border-color .15s, transform .15s;
}
.mt-saved-card:hover { border-color: rgba(18,85,192,.45); transform: translateY(-2px); }
.mt-saved-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(5,12,26,.1) 20%, rgba(5,12,26,.88) 100%);
}
.mt-saved-top { position: absolute; top: 0.6rem; left: 0.6rem; z-index: 2; }
.mt-badge {
    display: inline-block;
    font-style: normal;
    font-size: 0.62rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid;
}
.mt-badge-green  { background: rgba(52,211,153,.15); color: #34d399; border-color: rgba(52,211,153,.3); }
.mt-badge-orange { background: rgba(251,146,60,.15); color: #f97316; border-color: rgba(251,146,60,.3); }
.mt-badge-red    { background: rgba(248,113,113,.15); color: #f87171; border-color: rgba(248,113,113,.3); }
.mt-badge-live   { background: rgba(248,113,113,.18); color: #f87171; border-color: rgba(248,113,113,.35); animation: pulse-live 2s ease-in-out infinite; }
.mt-saved-info {
    position: absolute;
    left: 0.8rem; right: 0.8rem; bottom: 0.8rem;
    z-index: 2;
}
.mt-saved-info strong { display: block; font-size: 0.86rem; color: #fff; font-weight: 700; text-shadow: 0 2px 6px rgba(0,0,0,.5); }
.mt-saved-info small  { display: block; font-size: 0.68rem; color: rgba(200,220,255,.75); margin-top: 3px; }
.mt-saved-info em     { display: block; font-style: normal; font-size: 0.7rem; font-weight: 700; color: #7ab3f5; margin-top: 4px; }
.mt-unbookmark-form { position: absolute; top: 0.5rem; right: 0.5rem; z-index: 3; }
.mt-unbookmark-btn {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: rgba(5,12,26,.7);
    border: 1px solid rgba(255,255,255,.15);
    color: rgba(200,220,255,.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .12s, color .12s;
}
.mt-unbookmark-btn:hover { background: rgba(220,40,40,.8); color: #fff; border-color: transparent; }

/* ═══════════════════════════════════════
   TICKET MODAL
═══════════════════════════════════════ */
.tk-modal { position: fixed; inset: 0; z-index: 1200; display: none; }
.tk-modal.open { display: block; }
.tk-modal-backdrop { position: absolute; inset: 0; background: rgba(3,8,20,.78); backdrop-filter: blur(6px); }
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1rem;
    background: #0f1c2e;
    border-bottom: 1px solid #1e3050;
}
.tk-modal-head strong { color: #c0d4f0; font-size: 0.84rem; }
#ticket-modal-close {
    width: 30px; height: 30px;
    border: 1px solid #1e3050;
    border-radius: 8px;
    background: #1a2d45;
    color: #6b829e;
    font-size: 1.1rem;
    cursor: pointer;
    line-height: 1;
    transition: background .12s, color .12s;
}
#ticket-modal-close:hover { background: rgba(248,113,113,.2); color: #f87171; border-color: rgba(248,113,113,.4); }
#ticket-modal-frame { width: 100%; height: 100%; border: 0; }

/* ═══════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════ */
@media (max-width: 640px) {
    .mt-header-inner { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
    .mt-body { padding: 1rem 1rem 4rem; }
    .mt-ticket { grid-template-columns: 90px 1fr; min-height: 100px; }
    .mt-next-strip { flex-wrap: wrap; }
    .mt-next-right { flex-direction: row; align-items: center; width: 100%; justify-content: space-between; }
    .mt-saved-grid { grid-template-columns: 1fr 1fr; }
    .tk-modal-panel { width: calc(100% - 0.8rem); margin-top: 2vh; height: 93vh; }
}
@media (max-width: 400px) {
    .mt-saved-grid { grid-template-columns: 1fr; }
    .mt-ticket { grid-template-columns: 80px 1fr; }
}
</style>

<script>
(() => {
    // Filter pills
    const filters = document.querySelectorAll('.mt-filter');
    const wraps   = document.querySelectorAll('.mt-ticket-wrap');
    const noMatch = document.querySelector('.mt-no-match');

    filters.forEach(btn => {
        btn.addEventListener('click', () => {
            filters.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const f = btn.dataset.f;
            let visible = 0;
            wraps.forEach(w => {
                const show = f === 'all' || w.dataset.group === f;
                w.hidden = !show;
                if (show) visible++;
            });
            if (noMatch) noMatch.style.display = visible === 0 ? 'block' : 'none';
        });
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
        const link = e.target.closest('a.js-ticket-modal');
        if (link) { e.preventDefault(); openModal(link.href); }
        if (e.target.closest('[data-close-modal]')) closeModal();
    });
    if (modalClose) modalClose.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
})();
</script>
@endsection
