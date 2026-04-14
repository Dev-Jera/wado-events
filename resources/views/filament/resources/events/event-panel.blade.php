@php
    $event     = $record;
    $liveStatus = $event->live_status;
    $capacity  = $capacity ?? (int) $event->ticketCategories->sum('ticket_count');
    $editUrl   = route('filament.admin.resources.events.edit', ['record' => $event->id]);

    $statusColor = match ($liveStatus) {
        'live'      => '#16a34a',
        'upcoming'  => '#2563eb',
        'ended'     => '#64748b',
        'draft'     => '#d97706',
        'cancelled' => '#c8102e',
        default     => '#64748b',
    };
    $statusBg = match ($liveStatus) {
        'live'      => '#dcfce7',
        'upcoming'  => '#dbeafe',
        'ended'     => '#f1f5f9',
        'draft'     => '#fef3c7',
        'cancelled' => '#fee2e2',
        default     => '#f1f5f9',
    };
@endphp

<div class="ep" x-data="{ tab: 'overview' }">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="ep-header">
        <h2 class="ep-title">{{ $event->title }}</h2>
        @if ($event->venue)
            <p class="ep-meta">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $event->venue }}@if($event->city), {{ $event->city }}@endif
            </p>
        @endif
        @if ($event->starts_at)
            <p class="ep-meta">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ $event->starts_at->format('M d, Y') }}
            </p>
        @endif
    </div>

    {{-- ── Tabs ────────────────────────────────────────────────── --}}
    <div class="ep-tabs">
        <button class="ep-tab" :class="{ 'ep-tab--active': tab === 'overview' }" @click="tab = 'overview'">Overview</button>
        <button class="ep-tab" :class="{ 'ep-tab--active': tab === 'tickets' }"  @click="tab = 'tickets'">Tickets</button>
        <button class="ep-tab" :class="{ 'ep-tab--active': tab === 'attendees' }" @click="tab = 'attendees'">Attendees</button>
        <button class="ep-tab" :class="{ 'ep-tab--active': tab === 'analytics' }" @click="tab = 'analytics'">Analytics</button>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         OVERVIEW TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'overview'" x-cloak>

        {{-- Stats row --}}
        <div class="ep-stats-row">
            <div class="ep-stat">
                <span class="ep-stat-label">Tickets Sold</span>
                <strong class="ep-stat-val">{{ number_format($ticketsSold) }} <span class="ep-stat-of">/ {{ number_format($capacity) }}</span></strong>
            </div>
            <div class="ep-stat">
                <span class="ep-stat-label">Revenue</span>
                <strong class="ep-stat-val">UGX {{ number_format($revenue, 0) }}</strong>
            </div>
            <div class="ep-stat">
                <span class="ep-stat-label">Event Status</span>
                <span class="ep-status-badge" style="background: {{ $statusBg }}; color: {{ $statusColor }}">
                    {{ ucfirst($liveStatus) }}
                </span>
            </div>
        </div>

        {{-- Ticket Sales breakdown --}}
        <div class="ep-section">
            <h4 class="ep-section-title">Ticket Sales</h4>
            @forelse ($event->ticketCategories as $cat)
                @php
                    $catSold = (int) $cat->ticket_count - (int) $cat->tickets_remaining;
                    $catPct  = $cat->ticket_count > 0 ? ($catSold / $cat->ticket_count) * 100 : 0;
                @endphp
                <div class="ep-cat-row">
                    <div class="ep-cat-left">
                        <span class="ep-cat-name">{{ $cat->name }}</span>
                        <span class="ep-cat-price">UGX {{ number_format((float) $cat->price, 0) }}</span>
                    </div>
                    <div class="ep-cat-right">
                        <span class="ep-cat-count">{{ number_format($catSold) }} / {{ number_format($cat->ticket_count) }} sold</span>
                        <div class="ep-progress">
                            <div class="ep-progress-fill" style="width: {{ $catPct }}%"></div>
                        </div>
                    </div>
                    <div class="ep-cat-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                    </div>
                </div>
            @empty
                <p class="ep-empty">No ticket categories set up yet.</p>
            @endforelse
        </div>

        {{-- Recent Attendees --}}
        <div class="ep-section">
            <div class="ep-section-head">
                <h4 class="ep-section-title">Recent Attendees</h4>
            </div>
            @forelse ($recentAttendees as $txn)
                @php
                    $name  = $txn->holder_name ?: ($txn->user?->name ?? 'Unknown');
                    $email = $txn->user?->email ?? $txn->phone_number ?? '—';
                    $cat   = $txn->ticketCategory?->name ?? 'ticket';
                    $qty   = $txn->quantity;
                @endphp
                <div class="ep-attendee-row">
                    <div class="ep-avatar">{{ strtoupper(substr($name, 0, 2)) }}</div>
                    <div class="ep-attendee-info">
                        <span class="ep-attendee-name">{{ $name }}</span>
                        <span class="ep-attendee-sub">{{ $email }}</span>
                    </div>
                    <span class="ep-attendee-badge">{{ $qty }} {{ $cat }}</span>
                </div>
            @empty
                <p class="ep-empty">No attendees yet.</p>
            @endforelse

            @if ($recentAttendees->count() >= 5)
                <a href="{{ route('admin.events.show', $event) }}" class="ep-view-all">View All Attendees &rsaquo;</a>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════
         TICKETS TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'tickets'" x-cloak>
        <div class="ep-section">
            <h4 class="ep-section-title">Ticket Categories</h4>
            @forelse ($event->ticketCategories as $cat)
                @php
                    $catSold = (int) $cat->ticket_count - (int) $cat->tickets_remaining;
                @endphp
                <div class="ep-ticket-card">
                    <div>
                        <span class="ep-ticket-name">{{ $cat->name }}</span>
                        <span class="ep-ticket-meta">{{ $cat->ticket_count }} tickets &middot; Sort order {{ $cat->sort_order ?? 1 }}</span>
                    </div>
                    <span class="ep-ticket-price">UGX {{ number_format((float) $cat->price, 0) }}</span>
                </div>
            @empty
                <p class="ep-empty">No ticket categories yet.</p>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ATTENDEES TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'attendees'" x-cloak>
        <div class="ep-section">
            <h4 class="ep-section-title">All Attendees</h4>
            @forelse ($recentAttendees as $txn)
                @php
                    $name  = $txn->holder_name ?: ($txn->user?->name ?? 'Unknown');
                    $email = $txn->user?->email ?? $txn->phone_number ?? '—';
                    $cat   = $txn->ticketCategory?->name ?? 'ticket';
                    $qty   = $txn->quantity;
                @endphp
                <div class="ep-attendee-row">
                    <div class="ep-avatar">{{ strtoupper(substr($name, 0, 2)) }}</div>
                    <div class="ep-attendee-info">
                        <span class="ep-attendee-name">{{ $name }}</span>
                        <span class="ep-attendee-sub">{{ $email }}</span>
                    </div>
                    <span class="ep-attendee-badge">{{ $qty }} {{ $cat }}</span>
                </div>
            @empty
                <p class="ep-empty">No attendees yet.</p>
            @endforelse
            <a href="{{ route('admin.events.show', $event) }}" class="ep-view-all">View All Attendees &rsaquo;</a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ANALYTICS TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'analytics'" x-cloak>
        <div class="ep-section">
            @php
                $sellThrough = $capacity > 0 ? round(($ticketsSold / $capacity) * 100) : 0;
            @endphp
            <div class="ep-analytics-grid">
                <div class="ep-ana-card">
                    <span class="ep-stat-label">Sell-through</span>
                    <strong class="ep-stat-val">{{ $sellThrough }}%</strong>
                </div>
                <div class="ep-ana-card">
                    <span class="ep-stat-label">Revenue per ticket</span>
                    <strong class="ep-stat-val">
                        {{ $ticketsSold > 0 ? 'UGX ' . number_format($revenue / $ticketsSold, 0) : '—' }}
                    </strong>
                </div>
                <div class="ep-ana-card">
                    <span class="ep-stat-label">Capacity</span>
                    <strong class="ep-stat-val">{{ number_format($capacity) }}</strong>
                </div>
                <div class="ep-ana-card">
                    <span class="ep-stat-label">Available</span>
                    <strong class="ep-stat-val">{{ number_format(max(0, $capacity - $ticketsSold)) }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Footer actions ───────────────────────────────────────── --}}
    <div class="ep-footer">
        <a href="{{ $editUrl }}" class="ep-btn ep-btn--primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Event
        </a>
        <a href="{{ route('admin.events.show', $event) }}" class="ep-btn ep-btn--danger">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Delete Event
        </a>
    </div>

</div>

{{-- ── Styles ───────────────────────────────────────────────────── --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

[x-cloak] { display: none !important; }

.ep {
    font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
    display: flex;
    flex-direction: column;
    gap: 0;
    min-height: 100%;
}

/* ── Header ──────────────────────────────────────────────────── */
.ep-header {
    padding: 1.25rem 1.5rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    background: #0d1b3e;
}
.ep-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 .4rem;
    line-height: 1.2;
}
.ep-meta {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .75rem;
    color: rgba(255,255,255,.65);
    margin: .2rem 0 0;
}
.ep-meta svg { flex-shrink: 0; opacity: .7; }

/* ── Tabs ────────────────────────────────────────────────────── */
.ep-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
    padding: 0 1.5rem;
    overflow-x: auto;
}
.ep-tab {
    font-family: inherit;
    font-size: .75rem;
    font-weight: 600;
    padding: .7rem .9rem;
    border: none;
    background: none;
    color: #94a3b8;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    white-space: nowrap;
    transition: color .15s, border-color .15s;
}
.ep-tab:hover { color: #0d1b3e; }
.ep-tab--active { color: #0d1b3e; border-bottom-color: #c8102e; }

/* ── Section ─────────────────────────────────────────────────── */
.ep-section {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}
.ep-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .6rem;
}
.ep-section-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #94a3b8;
    margin: 0 0 .75rem;
}

/* ── Stats row ───────────────────────────────────────────────── */
.ep-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.ep-stat {
    display: flex;
    flex-direction: column;
    gap: .3rem;
    padding: .9rem 1rem;
    background: #fff;
}
.ep-stat-label {
    font-size: .65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #94a3b8;
}
.ep-stat-val {
    font-size: 1rem;
    font-weight: 800;
    color: #0d1b3e;
    line-height: 1.1;
}
.ep-stat-of {
    font-size: .8rem;
    font-weight: 500;
    color: #94a3b8;
}
.ep-status-badge {
    display: inline-block;
    padding: .2rem .6rem;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 700;
    margin-top: .1rem;
}

/* ── Ticket category rows ────────────────────────────────────── */
.ep-cat-row {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem 0;
    border-bottom: 1px solid #f8fafc;
}
.ep-cat-row:last-child { border-bottom: none; }
.ep-cat-left {
    display: flex;
    flex-direction: column;
    gap: .1rem;
    min-width: 80px;
}
.ep-cat-name  { font-size: .8rem; font-weight: 700; color: #0d1b3e; }
.ep-cat-price { font-size: .68rem; color: #64748b; }
.ep-cat-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: .3rem;
}
.ep-cat-count { font-size: .68rem; color: #64748b; text-align: right; }
.ep-progress  { height: 4px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
.ep-progress-fill { height: 100%; background: #0d1b3e; border-radius: 4px; transition: width .4s; }
.ep-cat-icon  { color: #cbd5e1; flex-shrink: 0; }

/* ── Attendee rows ───────────────────────────────────────────── */
.ep-attendee-row {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .5rem 0;
    border-bottom: 1px solid #f8fafc;
}
.ep-attendee-row:last-child { border-bottom: none; }
.ep-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #0d1b3e;
    color: #fff;
    font-size: .65rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.ep-attendee-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: .1rem;
    overflow: hidden;
}
.ep-attendee-name {
    font-size: .78rem;
    font-weight: 600;
    color: #0d1b3e;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ep-attendee-sub {
    font-size: .65rem;
    color: #94a3b8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ep-attendee-badge {
    font-size: .65rem;
    font-weight: 600;
    color: #2563eb;
    background: #dbeafe;
    padding: .15rem .5rem;
    border-radius: 999px;
    white-space: nowrap;
    flex-shrink: 0;
}

/* ── Ticket cards (Tickets tab) ──────────────────────────────── */
.ep-ticket-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .7rem .9rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: .5rem;
    background: #f8fafc;
}
.ep-ticket-name { display: block; font-size: .8rem; font-weight: 700; color: #0d1b3e; }
.ep-ticket-meta { font-size: .65rem; color: #94a3b8; }
.ep-ticket-price { font-size: .82rem; font-weight: 700; color: #0d1b3e; }

/* ── Analytics grid ──────────────────────────────────────────── */
.ep-analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}
.ep-ana-card {
    display: flex;
    flex-direction: column;
    gap: .3rem;
    padding: .9rem 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

/* ── View all / empty ────────────────────────────────────────── */
.ep-view-all {
    display: block;
    margin-top: .75rem;
    font-size: .72rem;
    font-weight: 600;
    color: #c8102e;
    text-decoration: none;
    text-align: center;
}
.ep-view-all:hover { opacity: .8; }
.ep-empty {
    font-size: .75rem;
    color: #94a3b8;
    text-align: center;
    padding: 1rem 0;
}

/* ── Footer ──────────────────────────────────────────────────── */
.ep-footer {
    display: flex;
    gap: .6rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #fff;
    margin-top: auto;
}
.ep-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .55rem 1rem;
    border-radius: 8px;
    font-family: inherit;
    font-size: .78rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: opacity .15s;
}
.ep-btn:hover { opacity: .85; }
.ep-btn--primary { background: #0d1b3e; color: #fff; flex: 1; justify-content: center; }
.ep-btn--danger  { background: #fee2e2; color: #c8102e; }
</style>
