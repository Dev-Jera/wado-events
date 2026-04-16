@php
    $liveStatus   = $event->live_status;
    $editUrl      = route('filament.admin.resources.events.edit', ['record' => $event->id]);
    $detailUrl    = route('admin.events.show', $event);
    $sellThrough  = $capacity > 0 ? round(($ticketsSold / $capacity) * 100) : 0;

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

<div class="edp" x-data="{ tab: 'overview' }">

    {{-- ── Dark navy header ───────────────────────────────────── --}}
    <div class="edp-header">
        @if ($event->category)
            <span class="edp-category-label">{{ strtoupper($event->category->name) }}</span>
        @endif
        <div class="edp-header-top">
            <h2 class="edp-title">{{ $event->title }}</h2>
            <button class="edp-close-btn" wire:click="closePanel" title="Close panel">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @if ($event->venue)
            <p class="edp-meta">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $event->venue }}@if($event->city), {{ $event->city }}@endif
            </p>
        @endif

        @if ($event->starts_at)
            <p class="edp-meta">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ $event->starts_at->format('M d, Y · g:i A') }}
            </p>
        @endif
    </div>

    {{-- ── Tabs ────────────────────────────────────────────────── --}}
    <div class="edp-tabs">
        <button class="edp-tab" :class="{ 'edp-tab--active': tab === 'overview' }"   @click="tab = 'overview'">Overview</button>
        <button class="edp-tab" :class="{ 'edp-tab--active': tab === 'tickets' }"    @click="tab = 'tickets'">Tickets</button>
        <button class="edp-tab" :class="{ 'edp-tab--active': tab === 'attendees' }"  @click="tab = 'attendees'">Attendees</button>
        <button class="edp-tab" :class="{ 'edp-tab--active': tab === 'analytics' }"  @click="tab = 'analytics'">Analytics</button>
    </div>

    {{-- ════════════════════════════════════════════════════════
         OVERVIEW TAB
    ════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'overview'" x-cloak>

        {{-- Stats strip --}}
        <div class="edp-stats">
            <div class="edp-stat">
                <span class="edp-stat-lbl">Tickets Sold</span>
                <strong class="edp-stat-val">{{ number_format($ticketsSold) }}<span class="edp-stat-of"> / {{ number_format($capacity) }}</span></strong>
            </div>
            <div class="edp-stat">
                <span class="edp-stat-lbl">Revenue</span>
                <strong class="edp-stat-val edp-stat-val--red">UGX {{ number_format($revenue, 0) }}</strong>
            </div>
            <div class="edp-stat">
                <span class="edp-stat-lbl">Status</span>
                <span class="edp-badge" style="background:{{ $statusBg }};color:{{ $statusColor }}">{{ ucfirst($liveStatus) }}</span>
            </div>
        </div>

        {{-- Ticket category breakdown --}}
        <div class="edp-section">
            <p class="edp-section-lbl">Ticket Sales</p>
            @forelse ($event->ticketCategories as $cat)
                @php
                    $sold    = (int)$cat->ticket_count - (int)$cat->tickets_remaining;
                    $pct     = $cat->ticket_count > 0 ? ($sold / $cat->ticket_count) * 100 : 0;
                @endphp
                <div class="edp-cat">
                    <div class="edp-cat-info">
                        <span class="edp-cat-name">{{ $cat->name }}</span>
                        <span class="edp-cat-price">UGX {{ number_format((float)$cat->price, 0) }}</span>
                    </div>
                    <div class="edp-cat-bar-wrap">
                        <div class="edp-cat-bar">
                            <div class="edp-cat-bar-fill" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="edp-cat-count">{{ number_format($sold) }} / {{ number_format($cat->ticket_count) }}</span>
                    </div>
                </div>
            @empty
                <p class="edp-empty">No ticket categories yet.</p>
            @endforelse
        </div>

        {{-- Recent attendees --}}
        <div class="edp-section">
            <p class="edp-section-lbl">Recent Attendees</p>
            @forelse ($recentAttendees as $txn)
                @php
                    $name = $txn->holder_name ?: ($txn->user?->name ?? 'Unknown');
                    $sub  = $txn->user?->email ?? $txn->phone_number ?? '—';
                    $cat  = $txn->ticketCategory?->name ?? 'ticket';
                @endphp
                <div class="edp-person">
                    <div class="edp-avatar">{{ strtoupper(substr($name, 0, 2)) }}</div>
                    <div class="edp-person-info">
                        <span class="edp-person-name">{{ $name }}</span>
                        <span class="edp-person-sub">{{ $sub }}</span>
                    </div>
                    <span class="edp-pill">{{ $txn->quantity }} {{ $cat }}</span>
                </div>
            @empty
                <p class="edp-empty">No attendees yet.</p>
            @endforelse
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════
         TICKETS TAB — full payment/ticket list for this event
    ════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'tickets'" x-cloak>

        {{-- Sell-through summary --}}
        <div class="edp-stats">
            <div class="edp-stat">
                <span class="edp-stat-lbl">Sell-through</span>
                <strong class="edp-stat-val">{{ $sellThrough }}%</strong>
            </div>
            <div class="edp-stat">
                <span class="edp-stat-lbl">Total sold</span>
                <strong class="edp-stat-val">{{ number_format($ticketsSold) }}</strong>
            </div>
            <div class="edp-stat">
                <span class="edp-stat-lbl">Revenue</span>
                <strong class="edp-stat-val">UGX {{ number_format($revenue, 0) }}</strong>
            </div>
        </div>

        {{-- Category cards --}}
        <div class="edp-section">
            <p class="edp-section-lbl">Categories</p>
            @foreach ($event->ticketCategories as $cat)
                @php $sold = (int)$cat->ticket_count - (int)$cat->tickets_remaining; @endphp
                <div class="edp-ticket-card">
                    <div>
                        <span class="edp-cat-name">{{ $cat->name }}</span>
                        <span class="edp-cat-price" style="display:block">{{ $cat->ticket_count }} tickets · Sort order {{ $cat->sort_order ?? 1 }}</span>
                    </div>
                    <div style="text-align:right">
                        <span class="edp-cat-name">UGX {{ number_format((float)$cat->price, 0) }}</span>
                        <span class="edp-cat-price" style="display:block">{{ $sold }} / {{ $cat->ticket_count }} sold</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Individual transactions --}}
        <div class="edp-section">
            <p class="edp-section-lbl">Ticket transactions</p>
            @forelse ($allTickets as $txn)
                @php
                    $name   = $txn->holder_name ?: ($txn->user?->name ?? 'Unknown');
                    $cat    = $txn->ticketCategory?->name ?? '—';
                    $status = strtolower($txn->status);
                    $bgMap  = ['confirmed'=>'#dcfce7','pending'=>'#fef3c7','failed'=>'#fee2e2'];
                    $clMap  = ['confirmed'=>'#16a34a','pending'=>'#92400e','failed'=>'#991b1b'];
                    $sbg    = $bgMap[$status] ?? '#f1f5f9';
                    $scl    = $clMap[$status] ?? '#64748b';
                @endphp
                <div class="edp-txn">
                    <div class="edp-avatar edp-avatar--sm">{{ strtoupper(substr($name,0,2)) }}</div>
                    <div style="flex:1;min-width:0">
                        <div class="edp-person-name">{{ $name }}</div>
                        <div class="edp-person-sub">{{ $cat }} &middot; qty {{ $txn->quantity }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div class="edp-cat-name">UGX {{ number_format((float)$txn->total_amount, 0) }}</div>
                        <span class="edp-badge" style="background:{{ $sbg }};color:{{ $scl }}">{{ $status }}</span>
                    </div>
                </div>
            @empty
                <p class="edp-empty">No ticket transactions yet.</p>
            @endforelse
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════
         ATTENDEES TAB
    ════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'attendees'" x-cloak>
        <div class="edp-section" style="padding-top:.5rem">
            <p class="edp-section-lbl">All recent attendees</p>
            @forelse ($recentAttendees as $txn)
                @php
                    $name = $txn->holder_name ?: ($txn->user?->name ?? 'Unknown');
                    $sub  = $txn->user?->email ?? $txn->phone_number ?? '—';
                    $cat  = $txn->ticketCategory?->name ?? 'ticket';
                @endphp
                <div class="edp-person">
                    <div class="edp-avatar">{{ strtoupper(substr($name, 0, 2)) }}</div>
                    <div class="edp-person-info">
                        <span class="edp-person-name">{{ $name }}</span>
                        <span class="edp-person-sub">{{ $sub }}</span>
                    </div>
                    <span class="edp-pill">{{ $txn->quantity }} {{ $cat }}</span>
                </div>
            @empty
                <p class="edp-empty">No attendees yet.</p>
            @endforelse

            <a href="{{ $detailUrl }}" class="edp-view-all">
                View full attendee list &rsaquo;
            </a>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         ANALYTICS TAB
    ════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'analytics'" x-cloak>
        @php
            $confirmedCount = \App\Models\PaymentTransaction::where('event_id', $event->id)->where('status', \App\Models\PaymentTransaction::STATUS_CONFIRMED)->count();
            $pendingCount   = \App\Models\PaymentTransaction::where('event_id', $event->id)->where('status', \App\Models\PaymentTransaction::STATUS_PENDING)->count();
            $failedCount    = \App\Models\PaymentTransaction::where('event_id', $event->id)->where('status', \App\Models\PaymentTransaction::STATUS_FAILED)->count();
            $totalTxns      = $confirmedCount + $pendingCount + $failedCount;
        @endphp

        {{-- Conversion stats --}}
        <div class="edp-stats">
            <div class="edp-stat">
                <span class="edp-stat-lbl">Sell-through</span>
                <strong class="edp-stat-val">{{ $sellThrough }}%</strong>
            </div>
            <div class="edp-stat">
                <span class="edp-stat-lbl">Confirmed</span>
                <strong class="edp-stat-val">{{ $confirmedCount }}</strong>
            </div>
            <div class="edp-stat">
                <span class="edp-stat-lbl">Pending</span>
                <strong class="edp-stat-val">{{ $pendingCount }}</strong>
            </div>
        </div>

        <div class="edp-section">
            <p class="edp-section-lbl">Transaction breakdown</p>

            {{-- Confirmed --}}
            <div class="edp-analytics-row">
                <span class="edp-analytics-label">
                    <span class="edp-analytics-dot" style="background:#16a34a"></span> Confirmed
                </span>
                <div class="edp-cat-bar" style="flex:1;margin:0 .75rem">
                    <div class="edp-cat-bar-fill" style="width:{{ $totalTxns > 0 ? round($confirmedCount/$totalTxns*100) : 0 }}%;background:#16a34a"></div>
                </div>
                <span class="edp-analytics-count">{{ $confirmedCount }}</span>
            </div>

            {{-- Pending --}}
            <div class="edp-analytics-row">
                <span class="edp-analytics-label">
                    <span class="edp-analytics-dot" style="background:#d97706"></span> Pending
                </span>
                <div class="edp-cat-bar" style="flex:1;margin:0 .75rem">
                    <div class="edp-cat-bar-fill" style="width:{{ $totalTxns > 0 ? round($pendingCount/$totalTxns*100) : 0 }}%;background:#d97706"></div>
                </div>
                <span class="edp-analytics-count">{{ $pendingCount }}</span>
            </div>

            {{-- Failed --}}
            <div class="edp-analytics-row">
                <span class="edp-analytics-label">
                    <span class="edp-analytics-dot" style="background:#c8102e"></span> Failed
                </span>
                <div class="edp-cat-bar" style="flex:1;margin:0 .75rem">
                    <div class="edp-cat-bar-fill" style="width:{{ $totalTxns > 0 ? round($failedCount/$totalTxns*100) : 0 }}%;background:#c8102e"></div>
                </div>
                <span class="edp-analytics-count">{{ $failedCount }}</span>
            </div>
        </div>

        <div class="edp-section">
            <p class="edp-section-lbl">Revenue per category</p>
            @foreach ($event->ticketCategories as $cat)
                @php
                    $catRev = \App\Models\PaymentTransaction::where('event_id', $event->id)
                        ->where('ticket_category_id', $cat->id)
                        ->where('status','CONFIRMED')
                        ->sum('total_amount');
                @endphp
                <div class="edp-cat">
                    <div class="edp-cat-info">
                        <span class="edp-cat-name">{{ $cat->name }}</span>
                        <span class="edp-cat-price">UGX {{ number_format((float)$catRev, 0) }}</span>
                    </div>
                    <div class="edp-cat-bar-wrap">
                        <div class="edp-cat-bar">
                            <div class="edp-cat-bar-fill" style="width:{{ $revenue > 0 ? round($catRev/$revenue*100) : 0 }}%"></div>
                        </div>
                        <span class="edp-cat-count">{{ $revenue > 0 ? round($catRev/$revenue*100) : 0 }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Footer action bar ───────────────────────────────────── --}}
    <div class="edp-footer">
        <a href="{{ $editUrl }}" class="edp-footer-btn">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Event
        </a>
        <a href="{{ $detailUrl }}" class="edp-footer-btn">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Full Details
        </a>
    </div>

</div>

{{-- ── Styles (scoped to .edp) ────────────────────────────── --}}
<style>
[x-cloak] { display: none !important; }

.edp {
    --ev-brand-blue: #0a4fbe;
    --ev-brand-blue-dark: #083f98;
    --ev-brand-blue-soft: #e8f0ff;
    font-family: 'Quicksand', 'Nunito', 'Plus Jakarta Sans', sans-serif;
}

/* Header */
.edp-header { background: var(--ev-brand-blue); padding: 1rem 1.1rem .85rem; }
.edp-category-label { display: inline-block; font-size: .58rem; font-weight: 700; letter-spacing: .1em; color: rgba(255,255,255,.5); text-transform: uppercase; margin-bottom: .3rem; }
.edp-header-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .5rem; margin-bottom: .3rem; }
.edp-title { font-size: 1rem; font-weight: 800; color: #fff; margin: 0; line-height: 1.25; }
.edp-close-btn { display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: rgba(255,255,255,.12); color: #fff; flex-shrink: 0; border: none; cursor: pointer; transition: background .15s; }
.edp-close-btn:hover { background: rgba(200,16,46,.6); }
.edp-meta { display: flex; align-items: center; gap: .3rem; font-size: .7rem; color: rgba(255,255,255,.6); margin: .22rem 0 0; }
.edp-meta svg { flex-shrink: 0; }

/* Tabs */
.edp-tabs { display: flex; border-bottom: 1px solid #e2e8f0; padding: 0 1.1rem; background: #fff; }
.edp-tab { font-family: inherit; font-size: .72rem; font-weight: 600; padding: .6rem .8rem; border: none; background: none; color: #94a3b8; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color .15s, border-color .15s; white-space: nowrap; }
.edp-tab:hover { color: #334155; }
.edp-tab--active { color: var(--ev-brand-blue); border-bottom-color: var(--ev-brand-blue); }

/* Stats strip — all white, revenue cell gets red value */
.edp-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: #e2e8f0; border-bottom: 1px solid #e2e8f0; }
.edp-stat { background: #fff; padding: .75rem .85rem; display: flex; flex-direction: column; gap: .2rem; }
.edp-stat-lbl { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; }
.edp-stat-val { font-size: .92rem; font-weight: 800; color: #334155; line-height: 1.1; }
.edp-stat-val--red { color: #c8102e; }
.edp-stat-of { font-size: .72rem; font-weight: 500; color: #94a3b8; }

/* Section */
.edp-section { padding: .85rem 1.1rem; border-bottom: 1px solid #f1f5f9; }
.edp-section-lbl { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin: 0 0 .65rem; }

/* Badge */
.edp-badge { display: inline-block; padding: .12rem .45rem; border-radius: 999px; font-size: .62rem; font-weight: 700; }

/* Category bar */
.edp-cat { display: flex; flex-direction: column; gap: .3rem; padding: .45rem 0; border-bottom: 1px solid #f8fafc; }
.edp-cat:last-child { border-bottom: none; }
.edp-cat-info { display: flex; justify-content: space-between; align-items: center; }
.edp-cat-name { font-size: .77rem; font-weight: 700; color: #334155; }
.edp-cat-price { font-size: .65rem; color: #64748b; }
.edp-cat-bar-wrap { display: flex; align-items: center; gap: .5rem; }
.edp-cat-bar { flex: 1; height: 5px; background: #e2e8f0; border-radius: 5px; overflow: hidden; }
.edp-cat-bar-fill { height: 100%; background: var(--ev-brand-blue); border-radius: 5px; transition: width .4s; }
.edp-cat-count { font-size: .62rem; color: #64748b; white-space: nowrap; }

/* Person row */
.edp-person { display: flex; align-items: center; gap: .6rem; padding: .45rem 0; border-bottom: 1px solid #f8fafc; }
.edp-person:last-of-type { border-bottom: none; }
.edp-avatar { width: 30px; height: 30px; border-radius: 50%; background: #e2e8f0; color: #475569; font-size: .62rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.edp-avatar--sm { width: 26px; height: 26px; font-size: .58rem; }
.edp-person-info { flex: 1; display: flex; flex-direction: column; gap: .05rem; overflow: hidden; }
.edp-person-name { font-size: .75rem; font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.edp-person-sub { font-size: .62rem; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.edp-pill { font-size: .62rem; font-weight: 600; color: var(--ev-brand-blue); background: var(--ev-brand-blue-soft); padding: .12rem .45rem; border-radius: 999px; white-space: nowrap; flex-shrink: 0; }

/* Transaction row */
.edp-txn { display: flex; align-items: center; gap: .6rem; padding: .5rem 0; border-bottom: 1px solid #f8fafc; }
.edp-txn:last-child { border-bottom: none; }

/* Ticket card */
.edp-ticket-card { display: flex; justify-content: space-between; align-items: center; padding: .6rem .8rem; border: 1px solid #e2e8f0; border-left: 3px solid var(--ev-brand-blue); border-radius: 8px; margin-bottom: .45rem; background: #fff; }

/* View all */
.edp-view-all { display: block; margin-top: .75rem; font-size: .7rem; font-weight: 600; color: var(--ev-brand-blue); text-decoration: none; text-align: center; }
.edp-view-all:hover { opacity: .75; }

/* Empty */
.edp-empty { font-size: .72rem; color: #94a3b8; padding: .75rem 0; text-align: center; }

/* Analytics rows */
.edp-analytics-row { display: flex; align-items: center; padding: .45rem 0; border-bottom: 1px solid #f8fafc; }
.edp-analytics-row:last-child { border-bottom: none; }
.edp-analytics-label { display: flex; align-items: center; gap: .35rem; font-size: .72rem; font-weight: 600; color: #334155; width: 80px; flex-shrink: 0; }
.edp-analytics-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.edp-analytics-count { font-size: .72rem; font-weight: 700; color: #334155; width: 24px; text-align: right; flex-shrink: 0; }

/* Footer */
.edp-footer { display: flex; gap: .6rem; padding: .85rem 1.1rem; border-top: 1px solid #e2e8f0; background: #fff; }
.edp-footer-btn { display: inline-flex; align-items: center; gap: .35rem; font-family: inherit; font-size: .72rem; font-weight: 600; padding: .5rem 1rem; border-radius: 7px; text-decoration: none; flex: 1; justify-content: center; border: 1.5px solid #cbd5e1; color: #334155; background: #fff; transition: border-color .15s, color .15s; }
.edp-footer-btn:hover { border-color: var(--ev-brand-blue); color: var(--ev-brand-blue); }
</style>
