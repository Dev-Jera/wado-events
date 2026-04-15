<x-filament-widgets::widget>
<div class="wado-dash">

    {{-- ══════════════════════════════════════════════════════════════
         1. NEEDS ATTENTION
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="wado-section">
        <p class="wado-section-label">NEEDS ATTENTION</p>
        <div class="wado-attention-grid">

            {{-- Pending payments --}}
            <a href="{{ route('filament.admin.resources.payment-transactions.index') }}" class="wado-attn-card wado-attn-card--amber">
                <span class="wado-attn-num">{{ number_format($pendingPayments) }}</span>
                <span class="wado-attn-title">Pending payments</span>
                <span class="wado-attn-sub">Awaiting mobile money</span>
            </a>

            {{-- Failed payments --}}
            <a href="{{ route('filament.admin.resources.payment-transactions.index') }}" class="wado-attn-card wado-attn-card--red">
                <span class="wado-attn-num">{{ number_format($failedPayments) }}</span>
                <span class="wado-attn-title">Failed payments</span>
                <span class="wado-attn-sub">Need follow-up</span>
            </a>

            {{-- QR not issued --}}
            <div class="wado-attn-card wado-attn-card--blue">
                <span class="wado-attn-num">{{ number_format($qrNotIssued) }}</span>
                <span class="wado-attn-title">QR not issued</span>
                <span class="wado-attn-sub">Confirmed, no QR yet</span>
            </div>

            {{-- At gate unscanned --}}
            <div class="wado-attn-card wado-attn-card--rose">
                <span class="wado-attn-num">{{ number_format($atGateUnscanned) }}</span>
                <span class="wado-attn-title">At gate &mdash; unscanned</span>
                <span class="wado-attn-sub">Issued, not validated</span>
            </div>

            {{-- Inventory remaining --}}
            <div class="wado-attn-card wado-attn-card--green">
                <span class="wado-attn-num">{{ number_format($inventoryRemaining) }}</span>
                <span class="wado-attn-title">Inventory remaining</span>
                <span class="wado-attn-sub">Unsold seats total</span>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         2. SUMMARY STATS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="wado-stats-row">

        <div class="wado-stat-card">
            <span class="wado-stat-label">Total events</span>
            <span class="wado-stat-num">{{ number_format($totalEvents) }}</span>
            @if ($newEventsThisMonth > 0)
                <span class="wado-stat-change wado-change--up">+{{ $newEventsThisMonth }} this month</span>
            @else
                <span class="wado-stat-change wado-change--neutral">No new events this month</span>
            @endif
        </div>

        <div class="wado-stat-card">
            <span class="wado-stat-label">Tickets sold</span>
            <span class="wado-stat-num">{{ number_format($ticketsSoldTotal) }}</span>
            @if ($ticketsPctChange !== 0)
                <span class="wado-stat-change {{ $ticketsPctChange >= 0 ? 'wado-change--up' : 'wado-change--down' }}">
                    {{ $ticketsPctChange >= 0 ? '+' : '' }}{{ $ticketsPctChange }}% vs last month
                </span>
            @else
                <span class="wado-stat-change wado-change--neutral">Same as last month</span>
            @endif
        </div>

        <div class="wado-stat-card">
            <span class="wado-stat-label">Revenue (UGX)</span>
            <span class="wado-stat-num">{{ number_format($revenueTotal, 0) }}</span>
            @if ($revenuePctChange !== 0)
                <span class="wado-stat-change {{ $revenuePctChange >= 0 ? 'wado-change--up' : 'wado-change--down' }}">
                    {{ $revenuePctChange >= 0 ? '+' : '' }}{{ $revenuePctChange }}% vs last month
                </span>
            @else
                <span class="wado-stat-change wado-change--neutral">Same as last month</span>
            @endif
        </div>

        <div class="wado-stat-card">
            <span class="wado-stat-label">Gate agents</span>
            <span class="wado-stat-num">{{ number_format($totalGateAgents) }}</span>
            @if ($inactiveAgents > 0)
                <span class="wado-stat-change wado-change--warn">{{ $inactiveAgents }} inactive</span>
            @else
                <span class="wado-stat-change wado-change--neutral">All active</span>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         3. EVENT OVERVIEW + TICKET SALES CHART
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="wado-two-col">

        {{-- Event overview table --}}
        <div class="wado-card wado-card--events">
            <div class="wado-card-header">
                <span class="wado-card-title">Event overview</span>
                <div class="wado-tab-group">
                    <span class="wado-tab wado-tab--active">By event</span>
                    <span class="wado-tab">Category</span>
                    <span class="wado-tab">Status</span>
                </div>
            </div>
            <div class="wado-table-wrap">
                <table class="wado-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th class="wado-th--num">Capacity</th>
                            <th class="wado-th--num">Sold</th>
                            <th class="wado-th--num">Revenue</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            <tr>
                                <td class="wado-td--title">{{ \Illuminate\Support\Str::limit($event['title'], 18) }}</td>
                                <td class="wado-td--num">{{ number_format($event['capacity']) }}</td>
                                <td class="wado-td--num">{{ number_format($event['sold']) }}</td>
                                <td class="wado-td--num">
                                    @if ($event['revenue'] > 0)
                                        UGX {{ number_format($event['revenue'], 0) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="wado-badge wado-badge--{{ $event['status'] }}">
                                        {{ $event['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="wado-td--empty">No events yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ticket sales chart --}}
        <div class="wado-card wado-card--chart">
            <div class="wado-card-header">
                <span class="wado-card-title">Ticket sales</span>
                <div class="wado-legend">
                    <span class="wado-legend-dot wado-legend-dot--blue"></span><span>Issued</span>
                    <span class="wado-legend-dot wado-legend-dot--teal"></span><span>Scanned</span>
                </div>
            </div>
            <div class="wado-chart-area">
                @foreach ($chartData as $bar)
                    <div class="wado-bar-group">
                        <div class="wado-bar-pair">
                            @php
                                $issuedPct  = $maxBarValue > 0 ? round(($bar['issued']  / $maxBarValue) * 100) : 0;
                                $scannedPct = $maxBarValue > 0 ? round(($bar['scanned'] / $maxBarValue) * 100) : 0;
                            @endphp
                            <div class="wado-bar wado-bar--blue"  style="height: max({{ $issuedPct }}%, 3px);"  title="Issued: {{ $bar['issued'] }}"></div>
                            <div class="wado-bar wado-bar--teal"  style="height: max({{ $scannedPct }}%, 3px);" title="Scanned: {{ $bar['scanned'] }}"></div>
                        </div>
                        <span class="wado-bar-label">{{ $bar['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         4. RECENT PAYMENTS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="wado-card wado-card--payments">
        <div class="wado-card-header">
            <span class="wado-card-title">Recent payments</span>
            <a href="{{ route('filament.admin.resources.payment-transactions.index') }}" class="wado-view-all">
                View all &rarr;
            </a>
        </div>
        <div class="wado-table-wrap">
            <table class="wado-table">
                <thead>
                    <tr>
                        <th>Holder</th>
                        <th>Event</th>
                        <th>Phone</th>
                        <th class="wado-th--num">Amount</th>
                        <th>Provider</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPayments as $payment)
                        <tr>
                            <td class="wado-td--title">{{ $payment['holder'] }}</td>
                            <td>{{ $payment['event'] }}</td>
                            <td class="wado-td--mono">{{ $payment['phone'] }}</td>
                            <td class="wado-td--num">{{ $payment['currency'] }} {{ number_format($payment['amount'], 0) }}</td>
                            <td>{{ $payment['provider'] }}</td>
                            <td>
                                <span class="wado-badge wado-badge--{{ $payment['status'] }}">
                                    {{ $payment['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="wado-td--empty">No transactions yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════════════════════ --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

/* ── Design tokens ──────────────────────────────────────────────── */
:root {
    --wado-navy:       #0d1b3e;
    --wado-navy-mid:   #152550;
    --wado-navy-light: #1e3460;
    --wado-red:        #c8102e;
    --wado-red-dim:    rgba(200,16,46,.12);
    --wado-white:      #ffffff;
    --wado-white-70:   rgba(255,255,255,.70);
    --wado-white-40:   rgba(255,255,255,.40);
    --wado-font:       'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ── Root container ─────────────────────────────────────────────── */
.wado-dash {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 0.25rem 0;
    font-family: var(--wado-font);
}

/* ── Section label ──────────────────────────────────────────────── */
.wado-section-label {
    font-family: var(--wado-font);
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--gray-400, #94a3b8);
    margin-bottom: .75rem;
}

/* ── Needs Attention grid ───────────────────────────────────────── */
.wado-attention-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: .75rem;
}
@media (max-width: 1100px) { .wado-attention-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 700px)  { .wado-attention-grid { grid-template-columns: repeat(2, 1fr); } }

.wado-attn-card {
    display: flex;
    flex-direction: column;
    gap: .25rem;
    padding: .85rem 1rem;
    border-radius: 10px;
    border: 1.5px solid var(--wado-navy-light);
    background: var(--wado-navy);
    text-decoration: none;
    transition: opacity .15s, transform .1s;
}
.wado-attn-card:hover { opacity: .88; transform: translateY(-1px); }

.wado-attn-num {
    font-family: var(--wado-font);
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1;
    color: var(--wado-white);
}
.wado-attn-title {
    font-family: var(--wado-font);
    font-size: .72rem;
    font-weight: 600;
    color: var(--wado-white-70);
}
.wado-attn-sub {
    font-family: var(--wado-font);
    font-size: .65rem;
    color: var(--wado-white-40);
}

/* ── Attention card colour variants ─────────────────────────────
   Layout: white | red | navy | white | navy
   Gives a natural mix of light and dark across the row.
──────────────────────────────────────────────────────────────── */

/* Pending payments — WHITE card, amber number */
.wado-attn-card--amber {
    border-color: #e5c073;
    background: #ffffff;
}
.wado-attn-card--amber .wado-attn-num   { color: #b45309; }
.wado-attn-card--amber .wado-attn-title { color: #1e293b; }
.wado-attn-card--amber .wado-attn-sub   { color: #64748b; }

/* Failed payments — solid RED card, white text */
.wado-attn-card--red {
    border-color: #a30d24;
    background: var(--wado-red);
}
.wado-attn-card--red .wado-attn-num   { color: #ffffff; }
.wado-attn-card--red .wado-attn-title { color: rgba(255,255,255,.88); }
.wado-attn-card--red .wado-attn-sub   { color: rgba(255,255,255,.62); }

/* QR not issued — DARK NAVY card, blue accent number */
.wado-attn-card--blue {
    border-color: #2d5fa8;
    background: var(--wado-navy);
}
.wado-attn-card--blue .wado-attn-num   { color: #93c5fd; }
.wado-attn-card--blue .wado-attn-title { color: var(--wado-white-70); }
.wado-attn-card--blue .wado-attn-sub   { color: var(--wado-white-40); }

/* At gate unscanned — WHITE card, red number */
.wado-attn-card--rose {
    border-color: #f5c6cc;
    background: #ffffff;
}
.wado-attn-card--rose .wado-attn-num   { color: var(--wado-red); }
.wado-attn-card--rose .wado-attn-title { color: #1e293b; }
.wado-attn-card--rose .wado-attn-sub   { color: #64748b; }

/* Inventory remaining — DARK NAVY card, green accent number */
.wado-attn-card--green {
    border-color: #1d6040;
    background: var(--wado-navy);
}
.wado-attn-card--green .wado-attn-num   { color: #86efac; }
.wado-attn-card--green .wado-attn-title { color: var(--wado-white-70); }
.wado-attn-card--green .wado-attn-sub   { color: var(--wado-white-40); }

/* ── Summary stats row ──────────────────────────────────────────── */
.wado-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .75rem;
}
@media (max-width: 900px) { .wado-stats-row { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .wado-stats-row { grid-template-columns: 1fr; } }

.wado-stat-card {
    display: flex;
    flex-direction: column;
    gap: .3rem;
    padding: 1rem 1.1rem;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
}

.wado-stat-label {
    font-family: var(--wado-font);
    font-size: .7rem;
    font-weight: 500;
    color: #64748b;
    text-transform: capitalize;
}
.wado-stat-num {
    font-family: var(--wado-font);
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--wado-navy);
    line-height: 1.1;
}
.wado-stat-change { font-family: var(--wado-font); font-size: .68rem; font-weight: 600; }
.wado-change--up      { color: #16a34a; }
.wado-change--down    { color: var(--wado-red); }
.wado-change--warn    { color: #d97706; }
.wado-change--neutral { color: #94a3b8; }

/* ── Two-column row ─────────────────────────────────────────────── */
.wado-two-col {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: .75rem;
    align-items: start;
}
@media (max-width: 900px) { .wado-two-col { grid-template-columns: 1fr; } }

/* ── Generic card ───────────────────────────────────────────────── */
.wado-card {
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    overflow: hidden;
}

.wado-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: .5rem;
    background: var(--wado-navy);
}

.wado-card-title {
    font-family: var(--wado-font);
    font-size: .8rem;
    font-weight: 700;
    color: #ffffff;
}

/* ── Tabs ───────────────────────────────────────────────────────── */
.wado-tab-group { display: flex; gap: .25rem; }
.wado-tab {
    font-family: var(--wado-font);
    font-size: .68rem;
    font-weight: 600;
    padding: .22rem .55rem;
    border-radius: 6px;
    color: var(--wado-white-70);
    cursor: pointer;
    user-select: none;
    transition: background .15s, color .15s;
}
.wado-tab:hover { background: var(--wado-navy-light); color: #ffffff; }
.wado-tab--active { background: var(--wado-red); color: var(--wado-white); }

/* ── Table ──────────────────────────────────────────────────────── */
.wado-table-wrap { overflow-x: auto; }
.wado-table {
    width: 100%;
    border-collapse: collapse;
    font-family: var(--wado-font);
    font-size: .76rem;
}
.wado-table thead th {
    padding: .5rem 1rem;
    text-align: left;
    font-family: var(--wado-font);
    font-size: .63rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #94a3b8;
    background: #f8fafc;
    white-space: nowrap;
}
.wado-th--num { text-align: right; }

.wado-table tbody tr {
    border-top: 1px solid #f1f5f9;
    transition: background .1s;
}
.wado-table tbody tr:hover { background: #f8fafc; }

.wado-table tbody td {
    padding: .6rem 1rem;
    color: #475569;
    white-space: nowrap;
    font-family: var(--wado-font);
}

.wado-td--title { font-weight: 600; color: var(--wado-navy) !important; }
.wado-td--num   { text-align: right; font-variant-numeric: tabular-nums; }
.wado-td--mono  { font-family: monospace; font-size: .72rem; }
.wado-td--empty { text-align: center; color: #94a3b8; padding: 1.5rem; }

/* ── Status badges ──────────────────────────────────────────────── */
.wado-badge {
    display: inline-block;
    padding: .15rem .55rem;
    border-radius: 999px;
    font-family: var(--wado-font);
    font-size: .63rem;
    font-weight: 700;
    text-transform: lowercase;
    white-space: nowrap;
}
.wado-badge--upcoming  { background: rgba(37,99,235,.25);  color: #93c5fd; border: 1px solid #2563eb44; }
.wado-badge--live      { background: rgba(22,163,74,.25);  color: #86efac; border: 1px solid #16a34a44; }
.wado-badge--ended     { background: #e2e8f0; color: #334155; border: 1px solid #cbd5e1; }
.wado-badge--draft     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.wado-badge--cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.wado-badge--confirmed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.wado-badge--pending   { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.wado-badge--failed    { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.wado-badge--initiated { background: #dbeafe; color: #1e3a8a; border: 1px solid #bfdbfe; }
.wado-badge--expired   { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.wado-badge--refunded  { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }

/* ── Chart ──────────────────────────────────────────────────────── */
.wado-card--chart { display: flex; flex-direction: column; }

.wado-legend {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-family: var(--wado-font);
    font-size: .68rem;
    color: #94a3b8;
}
.wado-legend-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.wado-legend-dot--blue { background: #3b82f6; }
.wado-legend-dot--teal { background: var(--wado-red); }

.wado-chart-area {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    gap: .3rem;
    padding: 1rem 1rem .5rem;
    height: 160px;
}
.wado-bar-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .3rem;
    flex: 1;
    height: 100%;
    justify-content: flex-end;
}
.wado-bar-pair {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    width: 100%;
    height: calc(100% - 1.2rem);
    justify-content: center;
}
.wado-bar {
    flex: 1;
    max-width: 16px;
    min-height: 3px;
    border-radius: 3px 3px 0 0;
    transition: opacity .15s;
}
.wado-bar:hover { opacity: .75; }
.wado-bar--blue { background: #3b82f6; }
.wado-bar--teal { background: var(--wado-red); }
.wado-bar-label {
    font-family: var(--wado-font);
    font-size: .6rem;
    color: #94a3b8;
    text-align: center;
    white-space: nowrap;
}

/* ── View all link ──────────────────────────────────────────────── */
.wado-view-all {
    font-family: var(--wado-font);
    font-size: .7rem;
    font-weight: 600;
    color: var(--wado-red);
    text-decoration: none;
    transition: opacity .15s;
}
.wado-view-all:hover { opacity: .75; }
</style>
</x-filament-widgets::widget>
